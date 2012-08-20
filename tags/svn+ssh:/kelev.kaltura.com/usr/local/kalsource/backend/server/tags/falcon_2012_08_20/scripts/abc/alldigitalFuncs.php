<?php
class AlldigitalFunctionality
{
    
    private $config;

    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    
        include (APPPATH.'libraries/Kaltura/KalturaClient.php');
    
        $this->ci->load->config('kaltura');
        $this->config = $this->ci->config->item('kaltura');
        $this->ci->load->driver('cache', array('adapter' => 'file'));
    
        log_message('debug', "Kaltura Library: Loaded");
     }
    
    public function exception_handler($exception)
    {
        log_message('debug', 'Kaltura Library Exception: '.$exception->getMessage());
    }
    
    public function error_handler($errno, $errstr)
    {
        log_message('debug', 'Kaltura Library Error: '.$errno.' '.$errstr);
    }
    
    public function transfer_start ($full_path,$conversion_profile_name=null)
    {
        $parsedFilePath = pathinfo($full_path);
        
        $incomingFilename = $parsedFilePath["basename"];
        	
        $conversionProfileName = null;
        if ($argc == 3)
        	$conversionProfileName = $argv[2];	
        
        $client = establishConnection($this->config);
        
        $entry = null;
        $entry = findMatchingEntry($incomingFilename, $client);
        
        
        if (!$conversionProfileName && !$entry)
        {
            $conversionProfileSysName = determineCPName($incomingFilename, $this->config, $cpNameParts);
            $cpId = determineCPId($conversionProfileSysName, $client);
            
            $entry = new KalturaMediaEntry();
            $entry->name = $entry->referenceId = $incomingFilename;
            $entry->mediaType = KalturaMediaType::VIDEO;
            $entry->conversionProfileId = $cpId;
            $entry = $client->media->add($entry);
            
            $mdProfile = $this->config["metadata_profile_id"];
            $fileNameParts = explode("_", $incomingFilename);
            
            $meta = "<metadata><Title>".$fileNameParts[1]."</Title><FormatType>".$this->config[$cpNameParts["base_name"]]["format_type"]."</FormatType><SourceType>".$cpNameParts["source_type"]."</SourceType><SourceVideoFile>$incomingFilename</SourceVideoFile></metadata>";
            $client->metadata->add($mdProfile, KalturaMetadataObjectType::ENTRY, $entry->id, $meta); 
            echo "Setting metadata profile $mdProfile entryid ".$entry->id ."XML $meta\r\n";
        }
        else if ($entry)
        {
            $client->media->update($entry->id, $entry);
        }
    }
    
    private function establishConnection ($ini_file)
    {
        $config = new KalturaConfiguration($ini_file["partner_id"]);
        $config->serviceUrl = $ini_file["service_url"];
        $client = new KalturaClient($config);
        $partnerId = $ini_file["partner_id"];
        $ks = $client->session->start($ini_file["admin_secret"], "", KalturaSessionType::ADMIN, $partnerId);
        $client->setKs($ks);
        return $client;
    }


    private function findMatchingEntry ($referenceId, $client)
    {
        $filter = new KalturaMediaEntryFilter();
        $filter->referenceIdEqual = $referenceId;
        $filter->statusIn = implode(',', array(
        	KalturaEntryStatus::ERROR_CONVERTING,
        	KalturaEntryStatus::ERROR_IMPORTING,
        	KalturaEntryStatus::IMPORT,
        	KalturaEntryStatus::NO_CONTENT,
        	KalturaEntryStatus::PENDING,
        	KalturaEntryStatus::PRECONVERT,
        	KalturaEntryStatus::READY,
        ));
        $listResult = $client->media->listAction($filter);
        $existingEntry = null;
        
        log_message('debug', "Kaltura Library transfer_end trace: Entries found by ReferenceIDEqual: ".$listResult->totalCount);
        if ($listResult->totalCount == 0)
        {
          log_message('debug', "Kaltura Library transfer_end trace: Exception unexpected number of entries");
          return null; // means not a single entry with the correct reference ID has been found..
        }

        log_message('debug', "Kaltura Library transfer_end trace: Entry already exists. Replacing: ". $listResult->objects[0]->id);
    	$existingEntry = $listResult->objects[0];
        
        return $existingEntry;
    }


    public function determineCPName ($fileName, $parsed_config, &$cpNameParts) 
    {
        
        $baseNameList = explode(",", $parsed_config["available_base_names"]);
        
        $sourceTypeList = explode(",", $parsed_config["available_source_types"]);
        
        $cpNameParts = array();
        
        $cpNameParts["base_name"] = $parsed_config["default"]["base_name"];
        
        foreach ($baseNameList as $baseName)
        {
            if (preg_match("/$baseName/i", $fileName))
            {
                $cpNameParts["base_name"] = $baseName;
            }
        }
        
        
        $cpNameParts["source_type"] = $parsed_config[$cpNameParts["base_name"]]["source_type"];
        
        foreach ($sourceTypeList as $sourceType)
        {
            if (preg_match("/$sourceType/i", $fileName))
            {
                 $cpNameParts["source_type"] = $sourceType;
            }
        }
        
        if (preg_match("/".$parsed_config["bug_keyword"]."/i", $fileName))
        {
            $cpNameParts["bug_action"] = $parsed_config["default"]["bug_action"];
        }
        else if (preg_match("/".$parsed_config["nobug_keyword"]."/i", $fileName))
        {
             if ($parsed_config["default"]["nobug_action"])
               $cpNameParts["bug_action"] = $parsed_config["default"]["nobug_action"];
        }
        else if ($parsed_config["default"]["bug_default_behavior"])
        {
              $cpNameParts["bug_action"] = $parsed_config["default"]["bug_action"];
        }
        else
        {
            if ($parsed_config["default"]["nobug_action"])
               $cpNameParts["bug_action"] = $parsed_config["default"]["nobug_action"];
        }
        
        return implode("_", $cpNameParts);
        
    }

    private function determineCPId ($systemName, $client)
    {
        $filter = new KalturaConversionProfileFilter;
        $filter->systemNameEqual = $systemName;
        $cps = $client->conversionProfile->listAction($filter);
        if ($cps->totalCount == 1) {
        	$cpid = $cps->objects[0]->id;
        }
        else {
            $client->stats->reportError ("Conversion Profile not found for system name [$systemName]");
        	throw new Exception("cannot find cpid sysname=$systemName");
        }
        return $cpid;
    }
    
    public function transfer_end($full_path)
    {
        set_exception_handler(array(
            &$this,
            'exception_handler'
        ));
        set_error_handler(array(
            &$this,
            'error_handler'
        ));
        $starttime = time();
        $incomingFilename = $full_path;
        $path_parts = pathinfo($incomingFilename);
        
        $client = establishConnection($this->config);
        
        $entry = findMatchingEntry($path_parts["base_name"], $client);
        
        if (!$entry)
        {
            $client->stats->reportError("Entry not found", "Cannot find matching entry for referenceId [". $path_parts["base_name"] ."]");
            throw new Exception("Cannot find matching entry for referenceId [". $path_parts["base_name"] ."]");
        }
        
        log_message('debug', "Kaltura Library transfer_end trace: EntryID: ".$entry->id);
        $resource = new KalturaRemoteStorageResource();
        $resource->storageProfileId = $this->config["source_storage_profile_id"];
        //$resource->url = $path_parts['basename'];
        $resource->url = $full_path;
        log_message('debug', "Kaltura Library transfer_end trace: URL ".$resource->url);
        
        
        $client->media->updateContent($entry->id, $resource);
        $endtime = time() - $starttime;
        log_message('debug', "Kaltura Library transfer_end Execution Time: ".$endtime."s");
        return true;
  }

}