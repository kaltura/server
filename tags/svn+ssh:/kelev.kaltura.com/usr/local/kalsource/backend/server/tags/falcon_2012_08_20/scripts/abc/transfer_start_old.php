<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');
  
/**
 * CodeIgniter Kaltura Class
 * Interface to Kaltura KMS
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Kon Wilms
 * @license         Copyright 2011 AllDigital, Inc. All Rights Reserved
 * @link            http://www.alldigital.com
 */

class Kaltura
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

  function exception_handler($exception)
  {
    log_message('debug', 'Kaltura Library Exception: '.$exception->getMessage());
  }

  function error_handler($errno, $errstr)
  {
    log_message('debug', 'Kaltura Library Error: '.$errno.' '.$errstr);
  }

  // start of transfer
  public function transfer_start($full_path,$conversion_profile_name=null)
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
    $conversionProfileName = $conversion_profile_name;
    $incomingFilename = $full_path;
    $path_parts = pathinfo($incomingFilename);
    list($date, $show, $sourceType, $contentType, $hasBug, $additional) = explode("_", $path_parts['basename'], 6);
    
    $show = strtolower($show);
    $contentType = strtolower($contentType);
    $sourceType = strtolower($sourceType);
    $hasBug = strtolower($hasBug);
    
    // Construct default cp system name, if none provided
    if ($conversionProfileName == null)
    {
      $conversionProfileName = $show."_".$sourceType."_".$contentType."_".$hasBug;
    }
    
    //Translate the contentType into the corresponding metadata value
    if ($contentType == 'sfp')
    {
      $contentType = 'Short Form';
    }
    elseif ($contentType == 'fep')
    {
      $contentType = 'Long Form';
    }
    
    $sourceType = strtoupper($sourceType);
    
    $config = new KalturaConfiguration($this->config["partner_id"]);
    $config->serviceUrl = $this->config["service_url"];
    $client = new KalturaClient($config);
    $partnerId = $this->config["partner_id"];    
    $ks = $client->session->start($this->config["admin_secret"], "", KalturaSessionType::ADMIN, $partnerId);
    $client->setKs($ks);
    $filter = new KalturaMediaEntryFilter();
    $filter->referenceIdEqual = $path_parts['basename'];
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
    if ($listResult->totalCount != 0)
    {
      log_message('debug', "Kaltura Library transfer_start Trace: Entry already exists. Replacing ".$listResult->objects[0]->id);
      $existingEntry = $listResult->objects[0];
    }
    else
    {
      $existingEntry = null;
    }
    
    $cpid = null;
    if (!$existingEntry)
    {
      log_message('debug', "Kaltura Library transfer_start Trace: cp sysname: ".$conversionProfileName);
      $filter = new KalturaConversionProfileFilter;
      $filter->systemNameEqual = $conversionProfileName;
      $cps = $client->conversionProfile->listAction($filter);
      if ($cps->totalCount == 1)
      {
	$cpid = $cps->objects[0]->id;
      }
      else
      {
	log_message('debug', "Kaltura Library transfer_start Trace: Exception, cannot find cpid sysname=".$conversionProfileName);
	return true; // means this already exists...
      }
    }
    
    $entry = new KalturaMediaEntry;
    $entry->name = $entry->referenceId = $path_parts['basename'];
    $entry->mediaType = KalturaMediaType::VIDEO;
    if ($cpid)
    {
      $entry->conversionProfileId = $cpid;
    }
    $mdprof = $this->config["metadata_profile_id"];
    if (!$existingEntry)
    {
      $adEntry = $client->media->add($entry);
      $meta = "<metadata><Title>$show</Title><FormatType>$contentType</FormatType><SourceType>$sourceType</SourceType><SourceVideoFile>".$path_parts['basename']."</SourceVideoFile></metadata>";
      $client->metadata->add($mdprof, KalturaMetadataObjectType::ENTRY, $adEntry->id, $meta);
      log_message('debug', "Kaltura Library transfer_start Trace: Setting Metadata Profile $mdprof, EntryID \"$adEntry->id\"");
      log_message('debug', "Kaltura Library transfer_start Trace: XML \"".$meta."\"");
    }
    else
    {
      $adEntry = $client->media->update($existingEntry->id, $entry);
      log_message('debug', "Kaltura Library transfer_start Trace: Used existing entry \"".$entry."\"");
    }
    $endtime = time() - $starttime;
    log_message('debug', "Kaltura Library transfer_start Execution Time: ".$endtime."s");
    return true;
  }

  // end of transfer
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
    $config = new KalturaConfiguration($this->config["partner_id"]);
    $config->serviceUrl = $this->config["service_url"];
    $client = new KalturaClient($config);
    $ks = $client->session->start($this->config["admin_secret"], "", KalturaSessionType::ADMIN);
    $client->setKs($ks);
    $filter = new KalturaMediaEntryFilter;
    $filter->referenceIdEqual = $path_parts['basename'];
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
    log_message('debug', "Kaltura Library transfer_end trace: Entries found by ReferenceIDEqual: ".$listResult->totalCount);
    if ($listResult->totalCount == 0)
    {
      log_message('debug', "Kaltura Library transfer_end trace: Exception unexpected number of entries");
      return true; // means this already exists... 
    }
    $entry = reset($listResult->objects);
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

/* End of file kaltura.php */
/* Location: ./application/libraries/kaltura.php */
