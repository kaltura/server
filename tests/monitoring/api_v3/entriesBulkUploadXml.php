<?php

class EntriesBulkUploadXml
{
	const JOB_STATUS_CODE_OK = 0;
	const JOB_STATUS_CODE_WARNING = 1;
	const JOB_STATUS_CODE_ERROR = 2;
	const TOKEN_CHAR = '@';
	const BULK_XML_FILE_ADD =  '/xml/entries_bulk_upload.xml';
	const BULK_XML_FILE_UPDATE =  '/xml/entries_bulk_upload_update_delete.xml';
	
	public $bulkError;
	public $monitorDescription;
	
	// replaces all tokens in the given string with the configuration values and returns the new string
	function replaceTokensInString($string, $values)
{
		foreach($values as $key => $var)
		{
			if(is_array($var))
				continue;

			$key = self::TOKEN_CHAR . $key . self::TOKEN_CHAR;
			$string = str_replace($key, $var, $string);
		}
		return $string;
	}

	function getBulkMonitorResult($client, $config) 
	{
			$monitorResult = new KalturaMonitorResult();
			$apiCall = null;

			try
			{
				$apiCall = 'session.start';
				$start = microtime(true);
				$ks = $client->session->start($config['monitor-partner']['secret'], 'monitor-user', KalturaSessionType::USER, $config['monitor-partner']['id']);
				$client->setKs($ks);
					
				$data = @file_get_contents(__DIR__ . self::BULK_XML_FILE_ADD);
				$entry1_ref_id = uniqid('monitor_bulk_xml1');
				$entry2_ref_id = uniqid('monitor_bulk_xml2');
				$entries_data = array(
					'ENTRY1_URL' => $client->getConfig()->serviceUrl . '/content/templates/entry/data/kaltura_logo_animated_green.flv',
					'ENTRY1_REF_ID' => $entry1_ref_id,
					'ENTRY2_URL' => $client->getConfig()->serviceUrl . '/content/templates/entry/data/kaltura_logo_animated_red.flv',
					'ENTRY2_REF_ID' => $entry2_ref_id,
				);
	
				$xml = self::replaceTokensInString($data, $entries_data);
				$xmlPath = sys_get_temp_dir() . "/" . uniqid('bulk_upload') . '.xml';
				file_put_contents($xmlPath, $xml);
				
				$this->bulkError = null;
				$this->monitorDescription = '';
				$bulkStatus;
				$apiCall = 'media.bulkUploadAdd';
				$jobData = new KalturaBulkUploadXmlJobData();
				$bulkUpload = $client->media->bulkUploadAdd($xmlPath, $jobData);

				$bulkUploadPlugin = KalturaBulkUploadClientPlugin::get($client);
				$bulkStatus = self::getBulkJobStatus("Add", $bulkUpload, $bulkUploadPlugin);
				
				if ($this->bulkError) {
					$bulkStatus = self::JOB_STATUS_CODE_ERROR;
					
					$error = new KalturaMonitorError();
					$error->description = "Add: " . $this->bulkError;
					$error->level = KalturaMonitorError::ERR;
				
					$monitorResult->errors[] = $error;
					$this->monitorDescription = $error->description;
				}
				//update entries
				else 
				{
					
					// create update users csv
					$data = @file_get_contents(__DIR__ . self::BULK_XML_FILE_UPDATE);
					$entries_data = array(
						'ACTION' => 'update',
						'ENTRY1_REF_ID' => $entry1_ref_id,
						'ENTRY1_DESC' => 'update monitor bulk upload xml 1',
						'ENTRY2_REF_ID' => $entry2_ref_id,
						'ENTRY2_DESC' => 'update monitor bulk upload xml 2',
					);
	
					$xml = self::replaceTokensInString($data, $entries_data);
					$xmlPath = sys_get_temp_dir() . "/" . uniqid('bulk_upload') . '.xml';
					file_put_contents($xmlPath, $xml);
				

					
					$this->bulkError = null;
					$apiCall = 'media.bulkUploadAdd';
					$jobData = new KalturaBulkUploadXmlJobData();
					$bulkUpload = $client->media->bulkUploadAdd($xmlPath, $jobData);
					/* @var $bulkUpload KalturaBulkUpload */

					$bulkUploadPlugin = KalturaBulkUploadClientPlugin::get($client);
					$bulkStatus = self::getBulkJobStatus("Update", $bulkUpload, $bulkUploadPlugin);
					
					if ($this->bulkError) {
						$bulkStatus = self::JOB_STATUS_CODE_ERROR;
						
						$error = new KalturaMonitorError();
						$error->description = 'Update: ' . $this->bulkError;
						$error->level = KalturaMonitorError::ERR;
					
						$monitorResult->errors[] = $error;
						$this->monitorDescription .= $error->description;
					}
					else {
						// create delete entries xml
						$data = @file_get_contents(__DIR__ . self::BULK_XML_FILE_UPDATE);
						$entries_data = array(
							'ACTION' => 'delete',
							'ENTRY1_REF_ID' => $entry1_ref_id,
							'ENTRY1_DESC' => 'update monitor bulk upload xml 1',
							'ENTRY2_REF_ID' => $entry2_ref_id,
							'ENTRY2_DESC' => 'update monitor bulk upload xml 2',
						);
		
						$xml = self::replaceTokensInString($data, $entries_data);
						$xmlPath = sys_get_temp_dir() . "/" . uniqid('bulk_upload') . '.xml';
						file_put_contents($xmlPath, $xml);
				

						$this->bulkError = null;
						$apiCall = 'media.bulkUploadAdd';
						$jobData = new KalturaBulkUploadXmlJobData();
						$bulkUpload = $client->media->bulkUploadAdd($xmlPath, $jobData);
						/* @var $bulkUpload KalturaBulkUpload */

						$bulkUploadPlugin = KalturaBulkUploadClientPlugin::get($client);
						$bulkStatus = self::getBulkJobStatus("Delete", $bulkUpload, $bulkUploadPlugin);
						
						if ($this->bulkError) {
							$bulkStatus = self::JOB_STATUS_CODE_ERROR;
							
							$error = new KalturaMonitorError();
							$error->description = 'Delete: ' . $this->bulkError;
							$error->level = KalturaMonitorError::ERR;
						
							$monitorResult->errors[] = $error;
							$this->monitorDescription .= $error->description;
						}
					}
				}

				$monitorResult->executionTime = microtime(true) - $start;
				$monitorResult->value = $bulkStatus;
				$monitorResult->description = $this->monitorDescription;
			}	
			catch(KalturaException $e)
			{
				$end = microtime(true);
				$monitorResult->executionTime = $end - $start;
				
				$error = new KalturaMonitorError();
				$error->code = $e->getCode();
				$error->description = $e->getMessage();
				$error->level = KalturaMonitorError::ERR;
				
				$monitorResult->errors[] = $error;
				$monitorResult->description = "Exception: " . get_class($e) . ", API: $apiCall, Code: " . $e->getCode() . ", Message: " . $e->getMessage();
			}
			catch(KalturaClientException $ce)
			{
				$end = microtime(true);
				$monitorResult->executionTime = $end - $start;
				
				$error = new KalturaMonitorError();
				$error->code = $ce->getCode();
				$error->description = $ce->getMessage();
				$error->level = KalturaMonitorError::CRIT;
				
				$monitorResult->errors[] = $error;
				$monitorResult->description = "Exception: " . get_class($ce) . ", API: $apiCall, Code: " . $ce->getCode() . ", Message: " . $ce->getMessage();
			}
			
			return $monitorResult;

	}
	
	function getBulkJobStatus($action, $bulkUpload, $bulkUploadPlugin)
	{
		$bulkStatus = null;
		while($bulkUpload)
		{
			if($bulkUpload->status == KalturaBatchJobStatus::FINISHED)
			{
				$bulkStatus = self::JOB_STATUS_CODE_OK;
				$this->monitorDescription .= "Entries Bulk Upload $action Job was finished successfully\n";
				break;
			}
			if($bulkUpload->status == KalturaBatchJobStatus::FINISHED_PARTIALLY)
			{
				$bulkStatus = self::JOB_STATUS_CODE_WARNING;
				$this->monitorDescription .= "Entries Bulk Upload $action Job Finished, but with some errors\n";
				break;
			}
			if($bulkUpload->status == KalturaBatchJobStatus::FAILED)
			{
				$this->bulkError =  "Bulk upload [$bulkUpload->id] failed";
				break;
			}
			if($bulkUpload->status == KalturaBatchJobStatus::ABORTED)
			{
				$this->bulkError = "Bulk upload [$bulkUpload->id] aborted";
				break;
			}
			if($bulkUpload->status == KalturaBatchJobStatus::FATAL)
			{
				$this->bulkError = "Bulk upload [$bulkUpload->id] failed fataly";
				break;
			}
			
			sleep(15);
			$bulkUpload = $bulkUploadPlugin->bulk->get($bulkUpload->id);
		}
		if(!$bulkUpload)
		{
				 $this->bulkError = "$action Bulk upload not found";
		}
		return $bulkStatus;
	}

}

$config = array();
$client = null;
/* @var $client KalturaClient */
require_once __DIR__  . '/common.php';

$options = getopt('', array(
	'service-url:',
	'debug',
));


$entriesBulkUploadXml = new EntriesBulkUploadXml();
echo $entriesBulkUploadXml->getBulkMonitorResult($client, $config);
exit(0);



