<?php

/**
 * IMAP mail checker.
 * Can connect to an IMAP mail server and retrive mail information and contents. *
 * 
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class KMailChecker {

	/**
	 * @var <string>
	 */
	private $hostname  = null;
	/**
	 * @var <int>
	 */
	private $port      = null;
	/**
	 * @var <string>
	 */
	private $username  = null;
	/**
	 * @var <string>
	 */
	private $password  = null;
	/**
	 * @var <resource>
	 */
	private $connection = null;
	/**
	 * Connect options
	 * @var <string>
	 */
	private $options = '';


	/**
	 * Constructor
	 *
	 * @param <string> $host    hostname
	 * @param <int>    $port    port number
	 * @param <string> $user    username
	 * @param <string> $pass    password
	 * @param <string> $options mail server options, see imap_open
	 */
	public function __construct ($host, $port, $user, $pass, $options = '')
	{
		$this->connection = null;
		$this->hostname   = $host;
		$this->port       = $port;
		$this->username   = $user;
		$this->password   = $pass;
		$this->options    = $options;
	}


	/**
	 * Destructor - closes the imap connection
	 */
	public function __destruct ()
	{
		if ($this->connection != null) {
			imap_close($this->connection);
		}
	}




	/**
	 * Open a connection with the defined server.
	 *
	 * @return <bool> true/false according to success
	 */
	public function connect()
	{
		//TODO: in the future, we can add an option for POP3 access as well

		$success = true;

		$mailbox_str = '{'.$this->hostname.':'.$this->port.$this->options.'}';

		if ($this->connection == null) {
			$this->connection = @imap_open($mailbox_str, $this->username, $this->password);
			$success = ($this->connection != null) && ($this->connection != false);
		}
		else {
			//reopen with a new mailbox name
			$success = imap_reopen($this->connection, $mailbox_str);
		}
		return $success;
	}


	/**
	 * Checkes for unread mails in the current mailbox
	 *
	 * @return <int> number of unread mails, or -1 on failure
	 */
	public function numUnread()
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}
		$status = imap_status($this->connection, '{'.$this->hostname.'}', SA_UNSEEN);
		if ($status) {
			return $status->unseen;
		}
		else {
			return -1;
		}
	}


	/**
	 * Get unread mail ids
	 *
	 * @return <string[]> array of id strings of all unread mails
	 */
	public function getUnreadIds()
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}
		//return imap_sort($this->connection, SORTARRIVAL, 1, SE_UID && SE_NOPREFETCH);
		return imap_search($this->connection, 'UNSEEN', SE_UID);
	}
	
	
	public function getFolders()
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}
		$temp = imap_list($this->connection, '{'.$this->hostname.'}', '*');
		$folders = array();
		foreach ($temp as $curFolder) {
			$folders[] = substr($curFolder, strpos($curFolder, '}')+1);
		}
		return $folders;
	}
	
	
	public function getFolderOverview($folder)
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}
		return imap_status($this->connection, '{'.$this->hostname.'}'.$folder, SA_MESSAGES + SA_RECENT + SA_UNSEEN);
	}	


	/**
	 * Moves a msg to a different folder
	 *
	 * @param <int> $msgId id number of the msg in current mailbox
	 * @param <string> $new_folder new folder's name
	 * @param <bool> $create create folder if doesn't exist ?
	 * @return <bool> true/false according to success
	 */
	public function moveMsg($msgId, $new_folder, $create=true)
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}
		if ($create) {
			@imap_createmailbox($this->connection, imap_utf7_encode('{'.$this->hostname.'}'.$new_folder));
		}
		$success = @imap_mail_move($this->connection, $msgId, $new_folder, CP_UID);
		@imap_expunge($this->connection);
		return $success;
	}


	/**
	 * Fetches the header, body & attachments of a msg.
	 *
	 * @param  <string> $msgId id string of the msg
	 * @return <MailMsg> the mail's data, or false on failure
	 */
	public function fetchMsg($msgId)
	{
		if (!$this->connectIfNeeded()) {
			return false;
		}

		$newMsg = new MailMsg();
		$structure = imap_fetchstructure($this->connection, $msgId, FT_UID);
		$msgNum = imap_msgno($this->connection, $msgId);
		if (!$structure) {
			return false;
		}
		else {
			// some parts might be set to 'false' if an error occured
			$newMsg->header      = $this->getHeader($msgNum);
			$newMsg->body        = $this->getBody($msgNum, $structure);
			$newMsg->attachments = $this->getAttachments($msgNum, $structure);
			return $newMsg;
		}
	}


	/**
	 * Returns data from a msg header
	 *
	 * @param  <int> $msgNum msg number in the current mailbox
	 * @return <MailHeader>
	 */
	private function getHeader($msgNum)
	{
		$header = new MailHeader();

		$headerInfo = imap_headerinfo($this->connection, $msgNum);
		if (!$headerInfo) {
			return false;
		}
		else {
			$header->fromadd = $this->headerToUtf8($headerInfo->from[0]->mailbox).'@'.$this->headerToUtf8($headerInfo->from[0]->host);
			$header->toadd = array();
			foreach ($headerInfo->to as $toAddress) {
				$header->toadd[] = $this->headerToUtf8($toAddress->mailbox).'@'.$this->headerToUtf8($toAddress->host);
			}		
			$header->msgid   = $this->headerToUtf8($headerInfo->message_id);
			$header->subject = $this->headerToUtf8($headerInfo->subject);

			return $header;
		}
	}




	/**
	 * Returns a msg body text with html tags striped
	 *
	 * @param  <int> $msgNum msg number in the current mailbox
	 * @param  <object> $structure mail structure recieved from imap_fetchstructure
	 * $param  <string> $prePart parents of the current structure (1.3 would be the parent of 1.3.1)
	 * @return <string> body text with html tags striped
	 */
	private function getBody($msgNum, &$structure = false, $prePart = '')
	{
		if( !$structure) {
			$structure = imap_fetchstructure($this->connection, $msgNum);
		}

		$body_plain = false;
		$body_html  = false;
		$more_parts = array();

		$prePart = trim($prePart);
		if (strlen($prePart) > 0) {
			$prePart = $prePart.'.';
		}


		if (!empty($structure->parts)) {
			$numParts = count($structure->parts);
			$i = 0;
			// scan all message parts, and loop of PLAIN or HTML text
			while (!$body_plain && $i < $numParts) {
				$part = $structure->parts[$i];
				if ($part->subtype == 'PLAIN') { // plain text
					$body_plain = $this->getBodyText($msgNum, $prePart.($i+1), $part->encoding, $this->findCharset($part));
				}
				else if ($part->subtype == 'HTML') { // html
					$body_html = $this->getBodyText($msgNum, $prePart.($i+1), $part->encoding, $this->findCharset($part));
					$body_html = strip_tags($body_html);
					str_replace('&lt;', '[', $body_html);
					str_replace('&gt;', ']', $body_html);
					str_replace('&#8221;', '"', $body_html);
				}
				else if (in_array(strtolower($part->subtype), array('alternative','related'))) { // different types of rich text messages
					$more_parts[$prePart.($i+1)] = $part;
				}
				$i++;
			}
		}
		else {
			$body_plain = $this->getBodyText($msgNum, $prePart.'1', $structure->encoding, $this->findCharset($structure));
		}

		$body = false;

		if ($body_plain) {
			$body = $body_plain;
		}
		else if ($body_html) {
			$body = $body_html;
		}
		else {
			// loop through all found ALTERNATIVE parts (mail might contain more than 1) and loop for the body text
			while (!$body && (list($partNum,$partStruct) = each($more_parts))) {
				$body = $this->getBody($msgNum, $partStruct, $partNum);
			}
		}

		return trim($body);
	}


	/**
	 * Fetch body text and handle decoding & charset conversion
	 * @param int $msgNum
	 * @param string $partStr
	 * @param int $encoding
	 * @param string $charset
	 * @return string body text
	 */
	private function getBodyText($msgNum, $partStr, $encoding, $charset)
	{
		$text = imap_fetchbody($this->connection, $msgNum, $partStr);
		$this->decode($text, $encoding);
		if ($charset) {
			$temp = iconv($charset, 'UTF-8', $text);
			if ($temp) {
				$text = $temp;
			}
		}
		return $text;
	}



	/**
	 * Returns all attachments of the given msg
	 * @param  <int> $msgNum msg number in the current mailbox
	 * @param  <object> $structure mail structure recieved from imap_fetchstructure
	 * $param  <string> $prePart parents of the current structure (1.3 would be the parent of 1.3.1)
	 * @return <MailAttachment[]> array mail's attachments
	 */
	private function getAttachments($msgNum, &$structure = false, $prePart = '')
	{
		if( !$structure) {
			$structure = imap_fetchstructure($this->connection, $msgNum);
		}

		$prePart = trim($prePart);
		if (strlen($prePart) > 0) {
			$prePart = $prePart.'.';
		}

		$attachments = array();
		$more_parts = array();

		if (isset($structure->parts) && count($structure->parts)) {

			// loop through all message parts and look for attachments
			for ($i = 0; $i < count($structure->parts); $i++) {

				$is_attachment = false;
				$filename      = null;

				if ($structure->parts[$i]->ifdparameters) {
					foreach($structure->parts[$i]->dparameters as $object) {
						if(strtolower($object->attribute) == 'filename') {
							$is_attachment = true;
							$filename      = $this->headerToUtf8($object->value);
						}
					}
				}

				if ($structure->parts[$i]->ifparameters) {
					foreach ($structure->parts[$i]->parameters as $object) {
						if(strtolower($object->attribute) == 'name') {
							$is_attachment = true;
							$filename      = $this->headerToUtf8($object->value);
						}
					}
				}

				if (!empty($structure->parts[$i]->parts)) {
					$more_parts[] = $i;
				}

				// if attachment found, decode it and save contents
				if ($is_attachment) {
					$content = imap_fetchbody($this->connection, $msgNum, $prePart.($i+1));
					$this->decode($content, $structure->parts[$i]->encoding);
					// add the new attachment to the returned array
					$curAttachment = new MailAttachment();
					$curAttachment->filename = $filename;
					$curAttachment->content  = $content;
					$curAttachment->type = $structure->parts[$i]->subtype;
					$attachments[] = $curAttachment;
				}

			}
		}

		foreach ($more_parts as $insidePart) {
			$more_attachments = $this->getAttachments($msgNum, $structure->parts[$insidePart], $prePart.($insidePart+1));
			if ($more_attachments && is_array($more_attachments)) {
				$attachments = array_merge($attachments, $more_attachments);
			}
		}

		return (count($attachments) > 0) ? $attachments : false;
	}




	/**
	 * Decodes the given content according to the mail encoding given
	 *
	 * @param <string> $content
	 * @param <int>    $encoding
	 */
	private function decode(&$content, $encoding)
	{
		switch ($encoding) {
			case 0:  // 0 = 7BIT
				// do nothing
				break;
			case 1:  // 1 = 8BIT
				// do nothing
				break;
			case 2:  // 2 = BINARY
				// do nothing
				break;
			case 3:  // 3 = BASE64
				$content = base64_decode($content);
				break;
			case 4: // 4 = QUOTED-PRINTABLE
				$content = quoted_printable_decode($content);
				break;
			case 5: // 5 = OTHER
				// do nothing
				break;
		}
	}


	/**
	 * Init connection if not already done
	 *
	 * @return <bool> true/false according to success
	 */
	private function connectIfNeeded()
	{
		if ($this->connection == null || !$this->connection) {
			return $this->connect();
		}
		else {
			return true; // already connected
		}
	}

	/**
	 * Decode text to UTF-8
	 *
	 * @param string $text
	 */
	private function headerToUtf8($text)
	{
		return iconv_mime_decode($text, 0, 'UTF-8');
	}


	private function findCharset(&$part)
	{
		$charset = false;
		if (isset($part->parameters)) {
			foreach ($part->parameters as $x) {
				if (strtolower($x->attribute) == 'charset') {
					$charset = $x->value;
					break;
				}
			}
		}
		if (isset($part->dparameters)) {
			foreach ($part->dparameters as $x) {
				if (strtolower($x->attribute) == 'charset') {
					$charset = $x->value;
					break;
				}
			}
		}
		return $charset;
	}




}


/**
 * Class holding all mail's data - header, body, attachments
 *  
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class MailMsg {
	/**
	 * @var MailHeader
	 */
	public $header;
	/**
	 * body text
	 * @var string
	 */
	public $body;
	/**
	 * @var MailAttachment[]
	 */
	public $attachments;

}


/**
 * Class holding a mail's header data
 *  
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class MailHeader {
	/**
	 * from address
	 * @var string
	 */
	public $fromadd;
	/**
	 * array of to addresses
	 * @var string[]
	 */
	public $toadd;
	/**
	 * msg id tag
	 * @var string
	 */
	public $msgid;
	/**
	 * mail's subject
	 * @var string
	 */
	public $subject;
}


/**
 * Class holding a mail's attachments data 
 * 
 * @package Scheduler
 * @subpackage Email-Ingestion
 */
class MailAttachment {
	/**
	 * file name
	 * @var string
	 */
	public $filename;
	/**
	 * type of attachment from mail's "Content-Type" parameter
	 * @var <type>
	 */
	public $type;
	/**
	 * file content
	 * @var string
	 */
	public $content;
}
