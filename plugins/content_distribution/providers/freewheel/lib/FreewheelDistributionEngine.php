<?php
/**
 * @package plugins.freewheelDistribution
 * @subpackage lib
 */
class FreewheelDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseSubmit
{
	const FREEWHEEL_SFTP_SERVER = "sftp.fwmrm.net";
	const FREEWHEEL_SFTP_PORT = 22;
	var $service_url;
    var $error;
    var $token;
    
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 * 
	 * Demonstrate asynchronous external API usage
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		// validates received object types
				
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelDistributionJobProviderData");
		
		// call the actual submit action
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		// always return false to be closed asynchronously by the closer
		return false;
	}

	protected function getSFTPManager(KalturaFreewheelDistributionProfile $distributionProfile)
	{
		$loginName = $distributionProfile->sftpLogin;
		$loginPass = $distributionProfile->sftpPass;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP, $engineOptions);
		$sftpManager->login(self::FREEWHEEL_SFTP_SERVER, $loginName, $loginPass);
		return $sftpManager;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$entryId = $data->entryDistribution->entryId;
		$loginName = $data->distributionProfile->sftpLogin;
		$loginPass = $data->distributionProfile->sftpPass;
		$filename = "files/bvi/log/".date("Y-m-d")."/".$entryId."_bvi_report.xml";
		$sftp_handle = ssh2_connect(self::FREEWHEEL_SFTP_SERVER, self::FREEWHEEL_SFTP_PORT);
		if(!$sftp_handle) {
			throw new Exception('Distribution failed with error [Could not connect to FTP server]');
		}
		$auth = ssh2_auth_password($sftp_handle, $loginName, $loginPass);
		if(!$auth) {
			throw new Exception('Distribution failed with error [Could not authorize to FTP server]');
		}
		$sftp_id = ssh2_sftp($sftp_handle);
		if(!$sftp_id) {
			throw new Exception('Distribution failed with error [Could not establish connection to FTP server]');
		}
		$stream = fopen("ssh2.sftp://" . intval($sftp_id) . "/$filename", 'r');
		if(!$stream) {
			throw new Exception('Distribution failed with error [Could not open stream to FTP server]');
		}
		$contents = fread($stream, 4096);
		if(!$contents) {
			throw new Exception('Distribution failed with error [Could not read from FTP server]');
		}
		$parser = xml_parser_create ();
		xml_parse_into_struct ( $parser, $contents, $values, $tags );
		xml_parser_free ( $parser );
		if(isset($tags['ERRORMESSAGE'])) {
			$errors = array();
			foreach ($tags['ERRORMESSAGE'] as $no=>$idx) {
				$errors[] = $values[$tags["ERRORNAME"][$no]]["value"].":".$values[$idx]["value"];
			}
		}
		if (isset($errors))
			throw new Exception('Distribution failed with error ['.$errors[0].']');
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		/*not implemented*/
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 * 
	 * demonstrate asynchronous XML delivery usage from template and uploading the media
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 * 
	 * Demonstrate asynchronous http url parsing
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO
		return false;
	}
	
	/**
	 * @param string $filename
	 * @param string $data
	 */
	public function upload($filename,$data) {
		$curl = curl_init();
	
		$random = rand();
		$boundary = "FW-boundary-${random}--------------";
		$headers = array("X-FreeWheelToken: $this->token",
				"Content-Type: multipart/form-data; boundary=$boundary",
				"Expect:",
				);
		
		$post_data = "--${boundary}\r\nContent-Disposition: form-data; name=\"upload_file[]\"; filename=\"${filename}\"\r\n";
		$post_data = $post_data."Content-Type: application/octet-stream\r\n\r\n"; 
		$post_data = $post_data.$data;
		$post_data = $post_data."--$boundary--";
	
		curl_setopt($curl, CURLOPT_URL,  $this->service_url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if ($result===false) {
		    $this->error = curl_error($curl);
		    curl_close($curl);
		    return false;
		}
		else {
		    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		    if ($code == 200) {
				$this->error = NULL;
				return $result;
		    }
		    $this->error = $result;
		    return false;
		}
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaFreewheelDistributionProfile $distributionProfile
	 * @param KalturaFreewheelDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaFreewheelDistributionProfile $distributionProfile, KalturaFreewheelDistributionJobProviderData $providerData)
	{
		$entryId = $data->entryDistribution->entryId;
		$partnerId = $distributionProfile->partnerId;
		$entry = $this->getEntry($partnerId, $entryId);

		// populate the external API object with the Kaltura entry data
		$mydata = array();
		$mydata["id"] = $entry->id;
		if(!empty($entry->name)) { $mydata["name"] = "<fwTitles><titleItem><title>".$entry->name."</title><titleType>Episode Title1</titleType></titleItem></fwTitles>"; }
		else { $mydata["name"] = ""; }
		if(!empty($entry->description)) { $mydata["description"] = "<fwDescriptions><descriptionItem><description>".$entry->description."</description><descriptionType>Episode</descriptionType></descriptionItem></fwDescriptions>"; }
		else { $mydata["description"] = ""; }
		if(!empty($entry->votes)) { $mydata["votes"] = "<datumItem><value>".$entry->votes."</value><label>votes</label></datumItem>"; }
		else { $mydata["votes"] = ""; }
		if(!empty($entry->plays)) { $mydata["plays"] = "<datumItem><value>".$entry->plays."</value><label>plays</label></datumItem>"; }
		else { $mydata["plays"] = ""; }
		if(!empty($entry->views)) { $mydata["views"] = "<datumItem><value>".$entry->views."</value><label>views</label></datumItem>"; }
		else { $mydata["views"] = ""; }
		if(!empty($entry->rank)) { $mydata["rank"] = "<datumItem><value>".$entry->rank."</value><label>rank</label></datumItem>"; }
		else { $mydata["rank"] = ""; }
		if(!empty($entry->totalRank)) { $mydata["total_rank"] = "<datumItem><value>".$entry->totalRank."</value><label>total_rank</label></datumItem>"; }
		else { $mydata["total_rank"] = ""; }
		if(!empty($entry->accessControlId)) { $mydata["access_control_id"] = "<datumItem><value>".$entry->accessControlId."</value><label>access_control_id</label></datumItem>"; }
		else { $mydata["access_control_id"] = ""; }
		if(!empty($entry->tags)) { $mydata["meta_tags"] = "<datumItem><value>".$entry->tags."</value><label>meta_tags</label></datumItem>"; }
		else { $mydata["meta_tags"] = ""; }
		if(!empty($entry->categories)) { $mydata["meta_categories"] = "<datumItem><value>".$entry->categories."</value><label>meta_categories</label></datumItem>"; }
		else { $mydata["meta_categories"] = ""; }
		if(!empty($entry->duration)) { $mydata["duration"] = "<fwDuration>$entry->duration</fwDuration>"; }
		else { $mydata["duration"] = ""; }
		if(!empty($entry->thumbnailUrl)) { $mydata["thumbnail"] = "<datumItem><value>".$entry->thumbnailUrl."</value><label>thumbnail_url</label></datumItem>"; }
		else { $mydata["thumbnail"] = ""; }
		
		$mydata["email"] = $distributionProfile->email;

		if(!empty($data->entryDistribution->sunrise) || !empty($data->entryDistribution->sunset)) {
			$mydata["dateavailable"] = "<fwDateAvailable>";
			if(!empty($data->entryDistribution->sunrise)) {
				$mydata["dateavailable"] .= "<dateAvailableStart>".date(DATE_ATOM,$data->entryDistribution->sunrise)."</dateAvailableStart>";
			}
			if(!empty($data->entryDistribution->sunset)) {
				$mydata["dateavailable"] .= "<dateAvailableEnd>".date(DATE_ATOM,$data->entryDistribution->sunset)."</dateAvailableEnd>";
			}
			$mydata["dateavailable"] .= "</fwDateAvailable>";
		} else {
			$mydata["dateavailable"] = "";
		}
		
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->partnerId, $data->entryDistribution->entryId, KalturaMetadataObjectType::ENTRY, $distributionProfile->metadataProfileId);

		$mediakey = $this->findMetadataValue($metadataObjects, 'MediaKey');
		$youtube = $this->findMetadataValue($metadataObjects, 'YouTube');
		$oldblockads = $this->findMetadataValue($metadataObjects, 'OldBlockAds');
		$mpm = $this->findMetadataValue($metadataObjects, 'MPM');
		$rating = $this->findMetadataValue($metadataObjects, 'ContentRating');

		if(!empty($mediakey)) { $mydata["meta_mediaKey"] = "<datumItem><value>".$this->findMetadataValue($metadataObjects, 'MediaKey')."</value><label>meta_mediaKey</label></datumItem>"; }
		else { $mydata["meta_mediaKey"] = ""; }
		if(!empty($youtube)) { $mydata["meta_YouTube"] = "<datumItem><value>".$this->findMetadataValue($metadataObjects, 'YouTube')."</value><label>meta_YouTube</label></datumItem>"; }
		else { $mydata["meta_YouTube"] = ""; }
		if(!empty($oldblockads)) { $mydata["meta_OldBlockAds"] = "<datumItem><value>".$this->findMetadataValue($metadataObjects, 'OldBlockAds')."</value><label>meta_OldBlockAds</label></datumItem>"; }
		else { $mydata["meta_OldBlockAds"] = ""; }
		if(!empty($mpm)) { $mydata["meta_MPM"] = "<datumItem><value>".$this->findMetadataValue($metadataObjects, 'MPM')."</value><label>meta_MPM</label></datumItem>"; }
		else { $mydata["meta_MPM"] = ""; }	
		if(!empty($rating)) { 
			switch($rating){
				case "MA":
					$rating = "Mature";
					break;
				case "PG":
				case "G":
					break;
				case "14":
					$rating = "PG-13";
					break;
				case "X":
					$rating = "R";
					break;
				default:
					$rating = "Unrated";
			}
		}
		else { 
			$rating = "Unrated";
		}
		$mydata["rating"] = "<fwRating>".$rating."</fwRating>";
		
		//do something
		$xmlTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/submit.template.xml';
		$handle = fopen($xmlTemplate, "r");
		$template = fread($handle, 4096);
		foreach ($mydata as $key=>$value) {
			$template = str_replace("%%".$key."%%", $value, $template);
		}
		fclose($handle);		

		$this->token = $distributionProfile->apikey;
		$this->service_url = "https://api.freewheel.tv/services/upload/bvi.xml";
		$result = $this->upload($entryId.".xml",$template);
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaFreewheelDistributionProfile $distributionProfile
	 * @param KalturaFreewheelDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaFreewheelDistributionProfile $distributionProfile, KalturaFreewheelDistributionJobProviderData $providerData)
	{
		// TODO
	}
}