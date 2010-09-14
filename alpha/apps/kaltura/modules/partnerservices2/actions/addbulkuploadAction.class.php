<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class addbulkuploadAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "addBulkUpload",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"profile_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					
					),
				"errors" => array (
					)
			);
	}

	protected function ticketType()
	{
		return self::REQUIED_TICKET_REGULAR;
	}

	// ask to fetch the kuser from puser_kuser - so we can tel the difference between a
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_KUSER_DATA;
	}

    protected function getObjectPrefix()
    {
    	return "";
    }
    
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$fileField = "csv_file";
		$profileId = $this->getP ( "profile_id", -1 );

		if (count($_FILES) == 0)
		{
			$this->addError(APIErrors::NO_FILES_RECEIVED);
			return;
		}
		
		if (!@$_FILES[$fileField]) 
		{
			$this->addError(APIErrors::INVALID_FILE_FIELD, $fileField);
			return;
		}
		
		// first we copy the file to "content/batchfiles/[partner_id]/"
		$origFilename = $_FILES[$fileField]['name'];

		$fileInfo = pathinfo($origFilename);
		$extension = strtolower($fileInfo['extension']);
		
		if ($extension != "csv")
		{
			$this->addError(APIErrors::INVALID_FILE_EXTENSION);
			return;
		}
		
		$job = new BatchJob();
		$job->setPartnerId($partner_id);
		$job->save();
		
		$data = new kBulkUploadJobData();
		$data->setCsvFilePath($_FILES[$fileField]['tmp_name']);
		$data->setUserId($puser_kuser->getPuserId());
		$data->setUploadedBy($puser_kuser->getPuserName());
		$data->setConversionProfileId($profileId);
			
		kJobsManager::addJob($job, $data, BatchJob::BATCHJOB_TYPE_BULKUPLOAD);
		
		$this->addMsg("status", "ok");
	}
}
?>