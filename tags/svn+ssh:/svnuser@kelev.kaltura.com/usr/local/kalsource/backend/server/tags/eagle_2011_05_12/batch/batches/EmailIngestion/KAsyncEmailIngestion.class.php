<?php
/**
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
require_once("bootstrap.php");
ini_set('memory_limit', '128M');

/**
 * Email ingestion batch.
 * Scans an IMAP mail server and adds all found attachments as partner entries.
 *
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class KAsyncEmailIngestion extends KBatchBase {


	/***************/
	/* Definitions */
	/***************/

	// temp folder to place attachments during processing
	private $TEMP_FILE_DIR = ''; // will be overwritten in run()

	// mailbox folders
	const NO_ATTACHMENT      = 'invalid';
	const PARTNER_INVALID    = 'invalid';
	const ATTACHMENT_INVALID = 'invalid';
	const ADD_ENTRY_FAIL     = 'failed';
	const PROCESS_OK         = 'processed';
	const INCOMPLETE         = 'incomplete';
	const UNKNOWN            = 'unkown_error';


	private $temp_files = array();



	/***********************************************/
	/* Implementation of parent ABSTRACT functions */
	/***********************************************/

	/**
	 * @return <number> batch type
	 */
	public static function getType()
	{
		return KalturaBatchJobType::EMAIL_INGESTION;
	}


	/**
	 * Batch is done running
	 */
	public function done ()
	{
		KalturaLog::info("Email ingestion batch DONE");
	}

	public function __destruct()
	{
		// deleting all remaining temporary files
		if ($this->temp_files) {
			foreach ($this->temp_files as $filename) {
				if (file_exists($filename)) {
					unlink($filename);
				}
			}
		}
	}

	protected function init()
	{ /* non-relevant abstract function */ }

	protected function updateExclusiveJob($jobId, KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }

	protected function freeExclusiveJob(KalturaBatchJob $job)
	{ /* non-relevant abstract function */ }


	/********/
	/* Main */
	/********/

	public function run ()
	{
		KalturaLog::info("Email ingestion batch is running");

		if($this->taskConfig->isInitOnly()) {
			return $this->init();
		}

		// get parameters from ini file
		try {
			$this->TEMP_FILE_DIR = $this->taskConfig->params->localTempPath;
		}
		catch (Exception $e) {
			KalturaLog::crit("Cannot find all required parameters from config file");
		}

		// create a temp file path
		if ( !self::createDir( $this->TEMP_FILE_DIR ) )
		{
			KalturaLog::crit( "Cannot continue email ingestion without a temp directory");
			return false; // quit run()
		}


		// ----------------------------------
		// loop through all defined mailboxes
		// ----------------------------------

		$mailboxNumber = 0;	

		while ( isset($this->taskConfig->params->{'mailbox'.($mailboxNumber+1)}) ) {

			$mailboxNumber++;
			$mailesProcessed = 0;
			$keepCurMailbox = true;

			$params = $this->taskConfig->params->{'mailbox'.$mailboxNumber};

			// get parameters
			try {
				$host      = $params->hostname;
				$port      = $params->port;
				$user      = $params->user;
				$pass      = $params->pass;
				$options   = $params->options;
				$maxMails  = $params->maxMailsPerRun;
			}
			catch (Exception $e) {
				KalturaLog::crit("Cannot find all required parameters from config file for mailbox number [$mailboxNumber]");
				continue; // skip current mailbox
			}


			// connect to current mailbox
			$mailChecker = new KMailChecker($host, $port, $user, $pass, $options);
			if (!$mailChecker->connect()) {
				KalturaLog::crit("Error connecting to [$host:$port] as [$user] - ".imap_last_error());
				continue; // skip current mailbox
			}
			KalturaLog::info("Sucessfuly connected to [$host:$port] as [$user]");
				
			// check for unread mails
			$newMails = $mailChecker->getUnreadIds();
			if (!$newMails || count($newMails) <= 0) {
				// no new mail availble in current mailbox
				KalturaLog::info("No new mails found on [$user@$host]");
				continue; // skip current mailbox
			}
			KalturaLog::info('['.count($newMails)."] unread mails found on [$user@$host]");

			// -----------------------------------------
			// loop through all mails in current mailbox
			// -----------------------------------------
			while ($keepCurMailbox && (list(,$curId) = each($newMails))) {

				if ($mailesProcessed >= $maxMails) {
					KalturaLog::info("Reached the max mails per job for current mailbox [$mailboxNumber] - skipping to next mailbox");
					$keepCurMailbox = false; // skip current mailbox
					continue; // skip current mail --> skip current mailbox
				}

				$mailesProcessed++;

				// fetch current message
				$curMail = $mailChecker->fetchMsg($curId);
				if (!$curMail) {
					KalturaLog::err("Error fetching message with folder ID [$curId] from [$user@$host] - ".imap_last_error());
					continue; // skip current mail - error fetching
				}

				// check if mail contains attachments
				if (!$curMail->attachments || count($curMail->attachments) == 0) {
					// no attachments found
					KalturaLog::info('No attachments found for mail ['.$curMail->header->msgid."] on [$user@$host] from [".$curMail->header->fromadd.'] with subject ['.$curMail->header->subject.']');
					if (!$mailChecker->moveMsg($curId, self::NO_ATTACHMENT)) {
						KalturaLog::err('Failed moving msg ['.$curMail->header->msgid.'] to the ['.self::NO_ATTACHMENT.'] folder - '.imap_last_error());
					}
					continue; // skip current mail - no attachments
				}
				
				
				// validate partner and get email profile
				$email_profiles = $this->validePartnerAndGetProfile($curMail->header->toadd, $user.'@'.$host);
				if (!$email_profiles) {
					// error validating partner
					KalturaLog::err('Partner validation failed for ['.$curMail->header->msgid."] on [$user@$host] from [".$curMail->header->fromadd.'] with subject ['.$curMail->header->subject.']');
					if (!$mailChecker->moveMsg($curId, self::PARTNER_INVALID)) {
						KalturaLog::err('Failed moving msg ['.$curMail->header->msgid.'] to the ['.self::PARTNER_INVALID.'] folder - '.imap_last_error());
					}
					continue; // skip current mail - partner invalid
				}

				// create a new media entry from data in mail body text
				$mediaEntry = $this->createMediaEntry($curMail->header, $curMail->body);

				// add the mail's attachment for each valid email profile
				$failures = new AddEntriesFailures();			
				foreach ($email_profiles as $profile) {		
					KalturaLog::info("*** Currently processing attachments for email profile id [$profile->id] of partner id [$profile->partnerId]");
					// add a new entry for each attachment
					//TODO: currently, the same attachment will be uploaded again and again for each different profile because the uploaded file is being transferred on the server - this should be changed.
					if (!$this->addEntries($curMail, $profile, $mediaEntry, $failures)) {
						KalturaLog::err("Some errors occured while adding entries for email profile id [$profile->id] of partner id [$profile->partnerId]");					
					}
				}
				
				// check if any problems happened			
				if ($failures->problemsHappened()) {
					$new_folder = self::UNKNOWN;
					if ($failures->upload_failed || $failures->add_entry_failed || $failures->error_saving_temp_file) {
						// some attachments had problems
						$new_folder = self::ADD_ENTRY_FAIL;
						KalturaLog::crit('Failed adding some attachments for ['.$curMail->header->msgid."] on [$user@$host] Moving msg to [".$new_folder.'] folder');
					}
					else if ($failures->too_many_attachments) {
						// too many attachments
						$new_folder = self::INCOMPLETE;
						KalturaLog::err('Msg ['.$curMail->header->msgid."] on [$user@$host] from [".$curMail->header->fromadd.'] with subject ['.$curMail->header->subject."] contains too many attachments. Moving msg to [$new_folder] folder");
					}
					else if ($failures->attachment_too_big || $failures->attachment_invalid) {
						// errors in specific attachments
						if (count($curMail->attachments) > 1) {
							$new_folder = self::INCOMPLETE;
							KalturaLog::err('Some invalid attachments found for msg ['.$curMail->header->msgid."] on [$user@$host] from [".$curMail->header->fromadd.'] with subject ['.$curMail->header->subject."]. Moving msg to [$new_folder] folder");
						}
						else {
							$new_folder = self::ATTACHMENT_INVALID;
							KalturaLog::err('Msg attachment is invalid for msg ['.$curMail->header->msgid."] on [$user@$host] from [".$curMail->header->fromadd.'] with subject ['.$curMail->header->subject."]. Moving msg to [$new_folder] folder");
						}
					}
					else {
						// shouldn't get here
						KalturaLog::err('*** Not all addEntriesFailures situations were handled.');
					}
					
					// move msg to the right error folder
					if (!$mailChecker->moveMsg($curId, $new_folder)) {
						KalturaLog::err('Failed moving msg ['.$curMail->header->msgid.'] to the ['.$new_folder.'] folder - '.imap_last_error());
					}			
				}
				
				else {
					// ------------------------------------------------------
					// all attachments were added succesfuly for all profiles
					// ------------------------------------------------------
					if (!$mailChecker->moveMsg($curId, self::PROCESS_OK)) {
						KalturaLog::err('Msg ['.$curMail->header->msgid.'] from ['.$curMail->header->fromadd.'] with subject ['.$curMail->header->subject.'] was processed OK but failed moving to the ['.self::PROCESS_OK.'] folder - '.imap_last_error());
					}
				}
								
			} // end loop through mails in current mailbox

		} // end loop through mailboxes

	}



	/**
	 * Validate partner by the toAddress and get the email ingestion profile
	 *
	 * @param <string> $toAddress
	 * @param <string> $mailbox user@host
	 * @return <KalturaEmailIngestionProfile>
	 */
	private function validePartnerAndGetProfile($toAddresses, $mailbox)
	{
		// get profiles for each to-address
		$email_profiles = array();	
		try {
			$this->getClient()->startMultiRequest();
			foreach ($toAddresses as $addr) {
				$this->getClient()->EmailIngestionProfile->getByEmailAddress($addr);				
			}
			$email_profiles = $this->getClient()->doMultiRequest();
		}
		catch (Exception $e) {
			// problem
			KalturaLog::err('There was an error getting email profiles from the server - '.$e->getMessage());
			$email_profiles = false;
		}
		
		// check for any valid profiles
		$valid_profiles = array();
		if ($email_profiles) {
			foreach ($email_profiles as $profile) {
				if ($profile && isset($profile->mailboxId) && $mailbox == $profile->mailboxId) {
					$valid_profiles[] = $profile;
				}
			}
		}

		// partner is validated
		if (count($valid_profiles) <= 0) {
			$valid_profiles = false;
		}
		return $valid_profiles;
	}


	/**
	 * Add new entries for all attachments under $mailData->attachments
	 *
	 * @param MailMsg $mailData
	 * @param KalturaEmailIngestionProfile $profile
	 * @param KalturaMediaEntry $mediaEntry media entry with data from mail's body
	 * @param AddEntriesFailures $failures
	 * @return <bool> true/false according to success
	 */
	private function addEntries(MailMsg &$mailData, KalturaEmailIngestionProfile $profile, KalturaMediaEntry $mediaEntry, AddEntriesFailures $failures)
	{
		$problems_happened = false;
		$entry_name = $mediaEntry->name;
		
		$this->kClientConfig->partnerId = $profile->partnerId;
		$this->kClient->setConfig($this->kClientConfig);
		
		// loop through all attachments
		// ----------------------------
		$num = 1;
		foreach ($mailData->attachments as $cur_attach) {
			
			if ($profile->maxAttachmentsPerMail && ($num > $profile->maxAttachmentsPerMail)) {
				KalturaLog::info ('Mail msg ['.$mailData->header->msgid.'] has more than ['.$profile->maxAttachmentsPerMail.'] attachments - ignoring the rest.');
				$problems_happened = true;
				$failures->too_many_attachments = true;				
				break; // quit adding attachments
			}
			
			if (!$this->validateAttachment($cur_attach, $errorMsg)) {
				$problems_happened = true;
				$failures->attachment_invalid = true;
				KalturaLog::err("Attachment [{$cur_attach->filename}] is not valid - $errorMsg");
				continue; // next attachment
			}

			// if no name set for entry, use the attchment filename		
			if ($entry_name == null) {
				$mediaEntry->name = $cur_attach->filename;
			}
			else if (count($mailData->attachments) > 1) {
				$mediaEntry->name = $entry_name . ' ('.$num.')';
			}

			// save a temporary file on the disk, named 'time()', so it will not be language dependent
			// ---------------------------------------------------------------------------------------
			$filename = $this->TEMP_FILE_DIR . DIRECTORY_SEPARATOR . $cur_attach->filename;
			$qpos = strpos($filename, "?");
			if ($qpos!==false) {
				$filename = substr($filename, 0, $qpos);
			}
			$extension = pathinfo($filename, PATHINFO_EXTENSION); // keep extension in order for addMediaEntry to work
			$filename = $this->TEMP_FILE_DIR.DIRECTORY_SEPARATOR.time().'.'.$extension;
			while (file_exists($filename)) {
				$filename = $this->TEMP_FILE_DIR.DIRECTORY_SEPARATOR.time().'.'.$extension;
			}
			KalturaLog::info("Attachment [{$cur_attach->filename}] is temporarly saved with name [$filename]");
			$this->temp_files[] = $filename; // keep a list of saved files that will be cleaned up during destruction
			$handle = fopen($filename, 'w');
			$fileWritten = $handle && fwrite($handle, $cur_attach->content);
			$fileWritten = $fileWritten && fclose($handle);
			if (!$fileWritten) {
				KalturaLog::err("Error writing to [$filename] in the temp directory.");
				$failures->error_saving_temp_file = true;
				$problems_happened = true;
				continue; // next attachment
			}
			
			// check if attachment size is valid according to the email ingestion profile configuration
			if ($profile->maxAttachmentSizeKbytes && (filesize($filename)/1024 > $profile->maxAttachmentSizeKbytes)) {
				KalturaLog::info("Attachment [$cur_attach->filename] is too big for profile [$profile->id] - ignoring.");
				// delete the temporary file from the disk			
				if (!unlink($filename)) {
					KalturaLog::info("Cannot delete [$filename] from the temp directory");
				}
				$failures->attachment_too_big = true;
				$problems_happened = true;
				continue; // next attachment
			}

			// upload file to the kaltura server
			// ---------------------------------
			try {
				$tokenId = $this->getClient()->upload->upload(realpath($filename)); 
			}
			catch (Exception $e) {
				$tokenId = null;
				KalturaLog::err($e->getMessage());
			}
			if ($tokenId == null || !$tokenId) {
				KalturaLog::err("Error uploading [$filename] to the kaltura server.");
				$problems_happened = true;
				$failures->upload_failed = true;
				// delete the temporary file from the disk
				if (!unlink($filename)) {
					KalturaLog::info("Cannot delete [$filename] from the temp directory");
				}
				continue; // next attachment
			}

			// create a new entry from the uploaded file
			// -----------------------------------------
			try {
				$newEntry = $this->getClient()->EmailIngestionProfile->addMediaEntry($mediaEntry, $tokenId, $profile->id, $mailData->header->fromadd, $mailData->header->msgid);
			}
			catch (Exception $e) {
				$newEntry = null;
				KalturaLog::err($e->getMessage());
			}
			if ($newEntry == null || !$newEntry) {
				KalturaLog::err("Error adding entry from uploaded file [$filename], token [$tokenId].");
				$problems_happened = true;
				$failures->add_entry_failed = true;
				// delete the temporary file from the disk
				if (!unlink($filename)) {
					KalturaLog::info("Cannot delete [$filename] from the temp directory");
				}
				continue; // next attachment
			}

			// entry created succesfully
			KalturaLog::info("New entry added succesfully with id [{$newEntry->id}]");
			$num++;

			// delete the temporary file from the disk
			if (!unlink($filename)) {
				KalturaLog::info("Cannot delete [$filename] from the temp directory");
			}
		}
		
		$mediaEntry->name = $entry_name;
		return !$problems_happened;
	}






	/**
	 * Create a new media entry with the required data from the mail's header & body
	 *
	 * @param MailHeader $mailHeader
	 * @param <string> $mailBody
	 * @return <KalturaMediaEntry>
	 */
	private function createMediaEntry(MailHeader $mailHeader, $mailBody)
	{
		$mediaEntry = new KalturaMediaEntry();
		$mailBody = str_ireplace(array('“','”'), '"', $mailBody);

		// create a new entry from the mail data
		$this->putIfNotNull($mediaEntry->name,				$mailHeader->subject);
		$this->putIfNotNull($mediaEntry->categories,		$this->getAndStrip('category', $mailBody));
		$this->putIfNotNull($mediaEntry->tags,				$this->getAndStrip('tags', $mailBody));
		$this->putIfNotNull($mediaEntry->adminTags,			$this->getAndStrip('admin_tags', $mailBody));
		$this->putIfNotNull($mediaEntry->ingestionProfileId,$this->getAndStrip('transcoding_profile_id', $mailBody));
		$this->putIfNotNull($mediaEntry->partnerData,		$this->getAndStrip('partner_data', $mailBody));
		$this->putIfNotNull($mediaEntry->userId,			$this->getAndStrip('user_id', $mailBody));
		// description must be last (contains all text left after striping above parameters
		$description = trim($mailBody);
		if (strlen($description) <= 0) {
			$description = null;
		}
		$description = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $description);
		$this->putIfNotNull($mediaEntry->description, $description);

		//remove 'TO:' and 'FW:' from media title
		$mediaEntry->name = trim(str_ireplace(array('FW:','RE:','FWD:'), '', $mediaEntry->name));

		return $mediaEntry;
	}


	/**
	 * Look for tags of type  <%tag%="%param">, strip them from the text, and return the param
	 *
	 * @param <string> $param
	 * @param <string> $mailBody
	 * @return <string> %param%
	 */
	private function getAndStrip($tag, &$mailBody)
	{
		$regexp = '/<'.$tag.'=[“”"].+[“”"]>/';
		$result = null;
		if (preg_match($regexp, $mailBody, $regs)) {
			$result = $regs[0];
			$mailBody = str_replace($result, "", $mailBody);
			$result = strstr($result, '"');
			$result = substr($result, 1, strlen($result)-3);
		}
		return $result;
	}



	private function putIfNotNull(&$putTo, $putFrom)
	{
		if ($putFrom != null) {
			$putTo = $putFrom;
		}
	}


	private function validateAttachment(MailAttachment &$attach, &$errorMsg)
	{
		$isValid = true;
		if (strtolower($attach->type) == 'ms-tnef') {
			$isValid = false;
			$errorMsg = "Attachments of TNEF format are not supported";
		}
		return $isValid;
	}


}



/**
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class AddEntriesFailures {

	public $attachment_invalid     = false;
	public $attachment_too_big     = false;
	public $too_many_attachments   = false;
	public $upload_failed          = false;
	public $add_entry_failed       = false;
	public $error_saving_temp_file = false;
	
	public function problemsHappened() {
		return $this->attachment_invalid   || $this->attachment_too_big ||
			   $this->too_many_attachments || $this->upload_failed      ||
			   $this->add_entry_failed     || $this->error_saving_temp_file;
	}
}
