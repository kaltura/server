<?php

print("Usage for specific partner(1234): php puser_kuser_consolidation 1234\n");
print("Usage for all partners: php puser_kuser_consolidation\n");
print("In Order to run a real run just type realRun in the end\n");

require_once(dirname(__FILE__).'/../bootstrap.php');

/**
 * 
 * Holds all neccessary puser details
 * @author Roni
 *
 */
class puserDetails
{
	public function __construct($puserId = null, $partnerId = null)
	{
		$this->puserId = $puserId;
		$this->partnerId = $partnerId; 
	}
	
	/**
	 * 
	 * The puser Id 
	 * @var string
	 */
	public $puserId;
	
	/**
	 * 
	 * The puser partner
	 * @var int
	 */
	public $partnerId;
}

/**
 * 
 * Consolidates all the pusers in the system
 * Fixes the puser to kuser issues 
 * @author Roni
 *
 */
class puserKuserConsolidator
{
	/**
	 * 
	 * The partners for which to ignore during the consolidation
	 * @var unknown_type
	 */
	private $ignorePartners = array(0, 99, 100);
	
	/**
	 * 
	 * Says if this is a real run or a dry run
	 * @var bool
	 */	
	private $isDryRun = true;
	
	/**
	 * 
	 * The max log file size
	 * @var int
	 */
	const MAX_LOG_FILE_SIZE = 50000;
	
	/**
	 * 
	 * The partner id to consolidate users on. (if null we do on all)
	 * @var int
	 */
	public $partnerId = null; 
		
	/**
	 * 
	 * The log file name (for manual log rotation)
	 * @var string
	 */
	private $logFileName = "c:/opt/kaltura/app/scripts/puser_kuser_deprecation/deprecation.log"; 
	
	/**
	 * 
	 * The current log numbet
	 * @var int
	 */
	private $currentlogNumber = 0;
	
	/**
	 * 
	 * Limits the number of handled pusers
	 * @var unknown_type
	 */
	public $limit = 100;
	 
	/**
	 * 
	 * Count the number of handled pusers
	 * @var unknown_type
	 */
	private $numOfHandledPusers = 0;
	
	/**
	 * 
	 * Holds all handled pusers
	 * @var array
	 */
	private $handledPusers = array();
	
	/**
	 * 
	 * The created users (used for dry run)
	 * @var unknown_type
	 */
	private $createdKusers = array();
	
	/**
	 * 
	 * All the pusers in the system
	 * @var unknown_type
	 */
	private $pusers = array();

	/**
	 * 
	 * The last created at date for the last entry
	 * @var int
	 */
	private $lastEntryFile = 'puser_kuser_deprecation.last_entry';
	
	/**
	 * 
	 * The last id of the puser
	 * @var string
	 */
	private $lastPuserFile = 'puser_kuser_deprecation.last_puser';
	
	/**
	 * 
	 * The last id of the kuser
	 * @var string
	 */
	private $lastKuserFile = 'puser_kuser_deprecation.last_kuser';
		
	/**
	 * 
	 * Creates a new consolidator for the given partner Id
	 * @param int $partnerId
	 */
	public function __construct($partnerId = null)
	{
		$this->partnerId = $partnerId;
		if(file_exists("{$this->logFileName}.{$this->currentlogNumber}")) //Clean the log file
		{
			file_put_contents("{$this->logFileName}.{$this->currentlogNumber}", "");
		}
	}
	
	/**
	 * 
	 * Inits the class with the given args from the command line
	 * @param array $argv
	 */
	public function initArgs($args)
	{
		$partnerId = null;
		$numOfArgs = count($args);
		
		if(isset($args[1]) && is_numeric($args[1]))
		{
			$this->partnerId = $args[1];
		}
		
		//Gets the last parameter
		
		$lastArg = $args[$numOfArgs-1];
		$isDryRun = true;
		if($lastArg == 'realRun')
		{
			$isDryRun = false;
		}
	}
	
	/**
	 * 
	 * Gets all the kusers from the puser_ kuser table by the given puser id and partner id
	 * @param string $puserId
	 * @param int $partnerId
	 */
	private function getKusersFromPuserKuserTable($puserId, $partnerId)
	{
		PuserKuserPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(PuserKuserPeer::PUSER_ID, $puserId, Criteria::EQUAL);
		$c->add(PuserKuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->add(PuserKuserPeer::PARTNER_ID, $this->ignorePartners, Criteria::NOT_IN);
		PuserKuserPeer::setUseCriteriaFilter(false);
		$puserKusers = PuserKuserPeer::doSelect($c);
		PuserKuserPeer::setUseCriteriaFilter(true);
		
		$kusers = array();
		
		foreach($puserKusers as $puserKuser) 
		{
			// now we check that the puser_kuser table doesn't reference to a different puser in the kuser table
			$kuser = kuserPeer::retrieveByPK($puserKuser->getKuserId());
			
			if(!is_null($kuser))
			{
				$kuserId = $kuser->getId();
				$kuserTablePuserId = $kuser->getPuserId();
				if(is_null($kuserTablePuserId))
				{
					$kuser->setPuserId($puserId);
										
					$this->printToLog("puserId [$puserId] in the Kuser table is null for kuser [$kuserId]- Perfect Match just set the kuser puser id");
					if(!$this->isDryRun)
					{
						$kuser->save();
					} 
				}
				
				if($kuserTablePuserId == $puserId && $kuser->getPartnerId() == $partnerId) // if this is the same partner and user
				{
					$kusers[] = $kuser; //add to the valid kusers
					$this->printToLog("Kuser [$kuserId] was added to the users found in the puser kuser table");
				}
				else // the puser on the ksuer are different from the kuser in the puser table
				{
					$kuserTablePuserId = $kuser->getPuserId();
					$puserTableKuserId = $puserKuser->getKuserId();
					
					$this->printToLog("We have a different kusers and pusers (Cross reference!!!)");
					$this->printToLog("partnerId [$partnerId], table puser_kuser: given puserId[$puserId] -> kuserId[$puserTableKuserId ], table kuser puserId [$kuserTablePuserId]");
				}
			}
			else //No such kuser
			{
				$kuserId = $puserKuser->getKuserId(); // the kuser id on the puser table
				$this->printToLog("Puser [$puserId], has kuser [$kuserId] and it can't be found on KUSER table");
			}
		}
		 
		return $kusers;
	}
	
	/**
	 * 
	 * Gets all the kusers from the kuser table
	 * @param string $puserId
	 * @param int $partnerId
	 */
	private function getKusersFromKuserTable($puserId, $partnerId)
	{
		kuserPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(kuserPeer::PUSER_ID, $puserId, Criteria::EQUAL);
		$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$c->add(kuserPeer::PARTNER_ID, $this->ignorePartners, Criteria::NOT_IN);
		kuserPeer::setUseCriteriaFilter(false);
		$kusers =  kuserPeer::doSelect($c);
		kuserPeer::setUseCriteriaFilter(true);
		return $kusers;
	}

	/**
	 * 
	 * Gets all entries for the given kuser
	 * @param int $kuserId - the kuser id
	 */
	private function getEntriesByKuser($kuserId)
	{
		entryPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(entryPeer::KUSER_ID, $kuserId, Criteria::EQUAL);
		$c->addAnd(entryPeer::PUSER_ID, null, Criteria::NOT_EQUAL);
		$c->addAnd(entryPeer::PUSER_ID, "", Criteria::NOT_EQUAL);
		
//		$c->setLimit($limit);
		entryPeer::setUseCriteriaFilter(false);
		$entries = entryPeer::doSelect($c);
		entryPeer::setUseCriteriaFilter(true);
		
		return $entries;
	}

	/**
	 * 
	 * Consolidates a given array of kusers to the first member in the kusers array
	 * @param array<kuser> $kusers
	 */
	private function consolidateKusers(array $kusers)
	{
		//Set the new kuser to be the first in teh array 
		$newKuser = $kusers[0];
		$newKuserId = $newKuser->getId();
		
		foreach ($kusers as $kuser)
		{
			$kuserId = $kuser->getId();
			$puserId = $kuser->getPuserId();
			$partnerId = $kuser->getPartnerId();
			
			if($kuserId == $newKuserId)
				continue;
			
			$entriesForKuser = $this->getEntriesByKuser($kuserId);
			foreach ($entriesForKuser as $entry)
			{
				$entryId = $entry->getId();
				$entryPuserId = $entry->getPuserId();
				$entryPartnerId = $entry->getPartnerId();
				if( $puserId == $entryPuserId && $partnerId == $entryPartnerId ) //if partner and puser are the same (as they should)
				{
					$entry->setKuserId($newKuserId);
					$this->printToLog("Changed EntryId [$entryId] from Kuser [$kuserId] to new Ksuer [$newKuserId for puser [$puserId], partner [$partnerId]\n");
					if(!$this->isDryRun)
					{
						$entry->save();
					}
				}
				else
				{
					$this->printToLog("EntryId [$entryId], entryPuser [$entryPuserId], entryPartner [$entryPartnerId] NOT CHANGED ".
					 				  "from Kuser [$kuserId] to new Ksuer [$newKuserId for puser [$puserId], partner [$partnerId]\n");
				}
			}
		}
	}

	/**
	 * 
	 * Gets or creates a kuser for the given puser id and partner id
	 * @param string $puserId
	 * @param int $partnerId
	 */
	private function getOrCreateKuser($puserId, $partnerId)
	{
//	if kuser table contains the puser only once =>
//		kuser = from kuser table
//	if kuser table contains the puser more than once =>
//		kuser = first kuser from table

//function getkuser(puser) Main algorithm:
//	if kuser table contains the puser =>
//		kuser = first from kuser table
//	else if kuserPuser contains the puser => 
//		kuser = from kuserPuser
//		if kuser table does not contain kuser
//			add kuser to table
//		else (Kuser contains)
//			if this is the same puser on the kuser table or null
//				Update the kuser table with the current puser id
//			else
//				//Print major conflict
//	else
//		create new kuser (+ optionally fix kuserPuser)

		//$this->printToLog("Getting or creating kuser for puser [$puserId], partner [$partnerId]");
		$kuser = null;
		
		$kusers = $this->getKusersFromKuserTable($puserId, $partnerId);
		//$this->printParam("kusers from kuser are: ", count($kusers));
		
		if($kusers && count($kusers) > 0)
		{
			//$this->printToLog("Kuser was found in the kuser table (maybe update the puser_kuser table)");
			
			$kuser = $kusers;
			if(is_array($kusers) && count($kusers) != 1)
			{
				$kuser = $kusers[0]; // gets the first kuser (if there are many)
				if(count($kusers) > 1) //if there are more then 1 or less then 1 Kuser we need to consolidate  / Create the kuser them all
				{
					$this->printToLog(count($kusers) . " were found in KUSER table for puser [$puserId], partner [$partnerId], needs to consolidate them all");
					$this->consolidateKusers($kusers);
				}
			}
		}
		else //Search the puser in puser_kuser table
		{
			//$this->printToLog(count($kusers) . " were found in KUSER table for puser [$puserId], partner [$partnerId], Searching in Puser Table");

			$kusers = $this->getKusersFromPuserKuserTable($puserId, $partnerId);
					
			$kuser = $kusers;
			if(is_array($kusers) && count($kusers) > 0)
			{
				$this->printToLog(count($kusers) . " were found in PUSER table for puser [$puserId], partner [$partnerId], needs to consolidate them all");
				$kuser = $kusers[0]; // gets the first kuser (if there are many)
			}
			else
			{
				if(count($kusers) == 0)
				{
					//$this->printToLog(count($kusers) . " kusers were found in PUSER table for puser [$puserId], partner [$partnerId]");
					//No kusers were found
				}
			}
		}
		
		if(is_null($kuser ) || (is_array($kuser) && count($kuser) == 0))
		{
			$this->printToLog("Kuser was not found!!! Creating new kuser for puser [$puserId], partner [$partnerId]");
			
			//no kuser found so we create one
			$this->createKuser($puserId, $partnerId);
		}
		
		return $kuser;
	}

	/**
	 * 
	 * Creates a new kuser and insert it into the kuser table
	 * @param string $puserId
	 * @param int $partnerId
	 */
	private function createKuser($puserId, $partnerId)
	{
		$kuser = new kuser();
		$kuser->partnerId = $partnerId;
		$kuser->puserId = $puserId;
		if($this->isDryRun)
		{
			$this->createdKusers["{$puserId}_{$partnerId}"] = $kuser;
		}
		else
		{
			$rowsAffected = $kuser->save();

			if($rowsAffected != 1)
			{
				$this->printToLog("Error in save: rows affected [$rowsAffected]");
			}
		}
		
	}
	
	/**
	 * 
	 * Prints a given meesage and param
	 * @param string $message
	 * @param unknown_type $param
	 */
	private function printParam($message, $param)
	{
		$this->printToLog($message . print_r($param, true));
	}
	
	/**
	 * 
	 * Gets all the pusers from the entry table
	 * @param int $lastEntryDate - the last entry date
	 * @param int $limit - the limit for the query
	 */
	private function getAllPusersInEntry($lastEntryDate, $limit)
	{
		$pusers = array();
		entryPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(entryPeer::CREATED_AT, $lastEntryDate, Criteria::GREATER_THAN);
		$c->addAnd(entryPeer::PUSER_ID, null, Criteria::NOT_EQUAL);
		$c->addAnd(entryPeer::PUSER_ID, "", Criteria::NOT_EQUAL);
		
		if($this->partnerId)
		{
			$c->addAnd(entryPeer::PARTNER_ID, $this->partnerId, Criteria::EQUAL);
		}
		
		$c->addAnd(entryPeer::PARTNER_ID, $this->ignorePartners, Criteria::NOT_IN);
		
		$c->addAscendingOrderByColumn(entryPeer::CREATED_AT);
		$c->setLimit($limit);
		entryPeer::setUseCriteriaFilter(false);
		$entries = entryPeer::doSelect($c);
		entryPeer::setUseCriteriaFilter(true);
		
		foreach ($entries as $entry)
		{
	//		$this->printToLog("Found entry with puser [{$entry->getPuserId()}], partner [{$entry->getPartnerId()}]");
			$pusers[] = new puserDetails($entry->getPuserId(), $entry->getPartnerId());
			
			file_put_contents($this->lastEntryFile, $entry->getCreatedAt());
		}
		
		return $pusers;
	}
		
	/**
	 * 
	 * Gets all the pusers from the puser table
	 * @param int $lastPuserId - the last puser id 
	 * @param int $limit - the limit for the query
	 */
	private function getAllPusersInPuser($lastPuserId, $limit)
	{
		$pusers = array();
		PuserKuserPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(PuserKuserPeer::ID, $lastPuserId, Criteria::GREATER_THAN);// if case we have several entries in the same date (and we stop in the middle)
		$c->addAnd(PuserKuserPeer::PUSER_ID, null, Criteria::NOT_EQUAL);
		$c->addAnd(PuserKuserPeer::PUSER_ID, "", Criteria::NOT_EQUAL);
		
		if($this->partnerId)
		{
			$c->addAnd(PuserKuserPeer::PARTNER_ID, $this->partnerId, Criteria::EQUAL);
		}
		
		$c->addAnd(PuserKuserPeer::PARTNER_ID, $this->ignorePartners, Criteria::NOT_IN);
		
		$c->addAscendingOrderByColumn(PuserKuserPeer::ID);
		$c->setLimit($limit);
		PuserKuserPeer::setUseCriteriaFilter(false);
		$pusers1 = PuserKuserPeer::doSelect($c);
		PuserKuserPeer::setUseCriteriaFilter(true);
				
		foreach ($pusers1 as $puser)
		{
		//	$this->printToLog("Found puser with id [{$puser->getId()}], partner [{$puser->getPartnerId()}]");
			$pusers[] = new puserDetails($puser->getPuserId(), $puser->getPartnerId());
			
			file_put_contents($this->lastPuserFile, $puser->getId());
		}
		
		return $pusers;
	}

	/**
	 * 
	 * Gets all the pusers from the kuser table
	 * @param int $lastKuserId - the last puser id 
	 * @param int $limit - the limit for the query
	 */
	private function getAllPusersInKuser($lastKuserId, $limit)
	{
		$pusers = array();
		kuserPeer::clearInstancePool();
		$c = new Criteria();
		$c->add(kuserPeer::ID, $lastKuserId, Criteria::GREATER_THAN);// if case we have several entries in the same date (and we stop in the middle)
		$c->addAnd(kuserPeer::ID, null, Criteria::NOT_EQUAL);
		$c->addAnd(kuserPeer::ID, "", Criteria::NOT_EQUAL);
		
		if($this->partnerId)
		{
			$c->addAnd(kuserPeer::PARTNER_ID, $this->partnerId, Criteria::EQUAL);
		}
		$c->addAnd(kuserPeer::PARTNER_ID, $this->ignorePartners, Criteria::NOT_IN);
		
		$c->addAscendingOrderByColumn(kuserPeer::ID);
		$c->setLimit($limit);
		kuserPeer::setUseCriteriaFilter(false);
		$kusers = kuserPeer::doSelect($c);
		kuserPeer::setUseCriteriaFilter(true);
				
		foreach ($kusers as $kuser)
		{
	//		$this->printToLog("Found puser with id [{$kuser->getPuserId()}], partner [{$kuser->getPartnerId()}] on Kuser [{$kuser->getId()}]");
			$pusers[] = new puserDetails($kuser->getPuserId(), $kuser->getPartnerId());
			
			file_put_contents($this->lastKuserFile, $kuser->getId());
		}
		
		return $pusers;
	}
	
	/**
	 * 
	 * Returns a list of all available pusers in the system by the given limit
	 * @param int $limit - the max number of entries / kusers / pusers to process
	 * @return puserDetails
	 */
	private function getAllPusersInTheSystem($limit)
	{
		$lastEntryDate = $this->getLastDate($this->lastEntryFile);
		$lastKuserId = $this->getLastId($this->lastKuserFile);
		$lastPuserId = $this->getLastId($this->lastPuserFile);
				
		$entryPusers = $this->getAllPusersInEntry($lastEntryDate, $limit);
		$puserPusers = $this->getAllPusersInPuser($lastPuserId, $limit);
		$kuserPusers = $this->getAllPusersInKuser($lastKuserId, $this->limit);
	
		$this->pusers = array_merge($this->pusers, $entryPusers);
		$this->pusers = array_merge($this->pusers, $puserPusers);
		$this->pusers = array_merge($this->pusers, $kuserPusers);

		return $this->pusers;
	}
	
	/**
	 * 
	 * Gets the last id of the given file (for entry, kuser, puser_kuser)
	 * @param string $file - the file path
	 */
	private function getLastId($file)
	{
		$lastId = 0;
		if(file_exists($file)) 
		{
			$lastId = file_get_contents($file);
			//$this->printToLog('file [$file] already exists with value - '.$lastId);
		}
		
		if(!$lastId)
			$lastId = 0;
		
		return $lastId;
	}
	
	/**
	 * 
	 * Gets the last date int the given file (for entry, kuser, puser_kuser)
	 * @param string $file - the file path
	 */
	private function getLastDate($file)
	{
		$lastDate = 0;
		if(file_exists($file)) 
		{
			$lastDate = file_get_contents($file);
			//$this->printToLog("file [$file] already exists with value - ".$lastDate);
		}
		
		if(!$lastDate)
			$lastDate = 0;
		
		return $lastDate;
	}

	/**
	 * 
	 * Consolidates all the pusers in the system (so each can have one kuser)
	 */
	public function consolidate()
	{
		$this->printToLog("Starting consolidation");
		
		$isMoreUsers = true;
		
		while($isMoreUsers)
		{
			$this->pusers = array();
			
			$pusers = $this->getAllPusersInTheSystem($this->limit);
						
			foreach($pusers as $puser)
			{
				$puserId = $puser->puserId;
				$partnerId = $puser->partnerId;
				
				if(in_array("{$puserId}_{$partnerId}", $this->handledPusers)) //if puser was handled we skip him
				{
					$kusers = $this->getKusersFromKuserTable($puserId, $partnerId);
					if(!is_null($kusers))
					{
						//$this->printToLog("Kuser is!!! : " . print_r($kuser, true));
						if(isset($this->createdKusers["{$puserId}_{$partnerId}"]))
						{
							//$this->printToLog("Puser [{$puserId}], partner [{$partnerId}] was handled KuserId [{$kuser->getId()}]");
						}
						elseif(is_array($kusers) && count($kusers) > 0)
						{
							$kuser = $kusers[0]; 
							//$this->printToLog("Puser [{$puserId}], partner [{$partnerId}] was handled KuserId [{$kuser->getId()}]");
						}
						else if(is_array($kusers) && count($kusers) == 0)
						{
							$this->printToLog("Puser [{$puserId}], partner [{$partnerId}] has no Kuser");
						}
					}
					else
					{
						$this->printToLog("Puser [{$puserId}], partner [{$partnerId}] was handled but Kuser is null");
					}
					
					continue;
				}
				
				$this->numOfHandledPusers++;
				$this->handledPusers["{$puserId}_{$partnerId}"] = "{$puserId}_{$partnerId}"; 
				
				
				$kuser = $this->getOrCreateKuser($puserId, $partnerId);
				
				if(is_null($kuser))
				{
					$this->printToLog("Kuser is null!!! for puser [{$puserId}], partner [{$partnerId}]");
					die(); // kill the script
				}
				
				//TODO: save the added puser / kuser 
				//file_put_contents($lastUserFile, $lastUser);
			}
			
			//$this->printToLog("Handled: ". count($pusers). " Pusers");
			if(count($pusers) == 0) // no more users
			{
				$isMoreUsers = false;
			}
		}
		
		$this->printToLog("Consolidation handled: {$this->numOfHandledPusers} pusers");
		return;
		
//Open issues:

//distribution: ask T
//	should use kuser_id or entry_id=>puser_id

//DWH:
//make sure that the updated at changes 
		
	}

	/**
	 * 
	 * Prints a message to the log. (rotate the log if it is too big)
	 * @param string $message
	 */
	private function printToLog($message)
	{
//		print("In print To Log \n");
		KalturaLog::debug($message);
//		$dirname = dirname(__FILE__);
//		$logFilePath = "{$this->logFileName}.{$this->currentlogNumber}";
//		print("Log file size: " . filesize($logFilePath) . "\n");
//		if(filesize($logFilePath) > puserKuserConsolidator::MAX_LOG_FILE_SIZE)
//		{
//			$this->rotateLog();
//		}
	}

	/**
	 * 
	 * Rotates the log into the new file 
	 */
	private function rotateLog()
	{
		$this->currentlogNumber++;
		
		try // we don't want to fail when logger is not configured right
		{
			$dirname = dirname(__FILE__);
			$logFilePath = "{$this->logFileName}.{$this->currentlogNumber}";
			$config = new Zend_Config_Ini("$dirname/logger.ini");
			$config->writers->stream->stream = $logFilePath;
		}
		catch(Zend_Config_Exception $ex)
		{
			$config = null;
		}
		
		KalturaLog::initLog($config);
	}
}

$puserKuserConsolidator = new puserKuserConsolidator();
$puserKuserConsolidator->initArgs($argv);
$puserKuserConsolidator->consolidate();