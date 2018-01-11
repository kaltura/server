<?php
/**
 * @package plugins.freewheelGenericDistribution
 * @subpackage lib
 */
class FreewheelGenericDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit,
	IDistributionEngineUpdate,
	IDistributionEngineCloseUpdate
{
	const FREEWHEEL_SFTP_SERVER = "sftp.fwmrm.net";
	const FREEWHEEL_SFTP_PORT = 22;
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 * 
	 * Demonstrate asynchronous external API usage
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		// validates received object types
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelGenericDistributionJobProviderData");
		
		// call the actual submit action
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		// always return false to be closed asynchronously by the closer
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		return $this->handleClose($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFreewheelGenericDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaFreewheelGenericDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaFreewheelGenericDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaFreewheelGenericDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		return $this->handleClose($data);
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaFreewheelGenericDistributionProfile $distributionProfile
	 * @param KalturaFreewheelGenericDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaFreewheelGenericDistributionProfile $distributionProfile, KalturaFreewheelGenericDistributionJobProviderData $providerData)
	{
		KalturaLog::err(print_r($providerData, true));
		$entryId = $data->entryDistribution->entryId;
		$partnerId = $distributionProfile->partnerId;
		
		$feed = new FreewheelGenericFeedHelper('freewheel_template.xml', $distributionProfile, $providerData);
		$xml = $feed->getXml();
		$data->sentData = $xml;
		$result = $this->upload($entryId.".xml", $xml, $distributionProfile->apikey);
		$data->results = $result;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function handleClose(KalturaDistributionJobData $data)
	{
		$entryId = $data->entryDistribution->entryId;
		$loginName = $data->distributionProfile->sftpLogin;
		$loginPass = $data->distributionProfile->sftpPass;
		
		$dateTime = new DateTime('now', new DateTimeZone('America/Los_Angeles')); // Freewheel is using Pacific Time
		$filename = "files/bvi/log/".$dateTime->format("Y-m-d")."/".$entryId."_bvi_report.xml";
		$sftp_handle = ssh2_connect(self::FREEWHEEL_SFTP_SERVER, self::FREEWHEEL_SFTP_PORT);
		if (!$sftp_handle)
			throw new Exception('Distribution failed with error [Could not connect to FTP server]');
		
		$auth = ssh2_auth_password($sftp_handle, $loginName, $loginPass);
		if (!$auth)
			throw new Exception('Distribution failed with error [Could not authorize to FTP server]');
		
		$sftp_id = ssh2_sftp($sftp_handle);
		if (!$sftp_id)
			throw new Exception('Distribution failed with error [Could not establish connection to FTP server]');
		
		$stream = fopen("ssh2.sftp://" . intval($sftp_id) . "/$filename", 'r');
		if (!$stream)
			return false; // file doesn't exist yet
		
		$contents = fread($stream, 4096);
		if (!$contents)
			return false; // count empty content as the file was not writed to the ftp yet
			
		$parser = xml_parser_create ();
		xml_parse_into_struct ( $parser, $contents, $values, $tags );
		xml_parser_free ( $parser );
		if(isset($tags['ERRORMESSAGE'])) 
		{
			$errors = array();
			foreach ($tags['ERRORMESSAGE'] as $no=>$idx) 
			{
				$errors[] = $values[$tags["ERRORNAME"][$no]]["value"].":".$values[$idx]["value"];
			}
		}
		if (isset($errors))
			throw new Exception('Distribution failed with error ['.$errors[0].']');
			
		$data->results = $contents;
		return true;
	}
	
	/**
	 * @param string $filename
	 * @param string $data
	 */
	public function upload($filename, $data, $token) 
	{
		KalturaLog::info('Sending the following XML:');
		KalturaLog::info($data);
		
		$curl = curl_init();
	
		$random = rand();
		$boundary = "FW-boundary-${random}--------------";
		$headers = array("X-FreeWheelToken: $token",
				"Content-Type: multipart/form-data; boundary=$boundary",
				"Expect:",
				);
		
		$postData = "--${boundary}\r\nContent-Disposition: form-data; name=\"upload_file[]\"; filename=\"${filename}\"\r\n";
		$postData = $postData."Content-Type: application/octet-stream\r\n\r\n"; 
		$postData = $postData.$data;
		$postData = $postData."--$boundary--";
	
		curl_setopt($curl, CURLOPT_URL,  "https://api.freewheel.tv/services/upload/bvi.xml");
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		KalturaLog::info('BVI result: [' . $result . ']');
		if ($result===false) 
		{
			$error = curl_error($curl);
			curl_close($curl);
			throw new Exception('Failed to send metadata to Freewheel BVI ['.$error.']');
		}
		else 
		{
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			KalturaLog::info('BVI returned HTTP status code ' . $code);
			if ($code == 200) 
				return $result;
				
			throw new Exception('HTTP error occured while sending metadata to Freewheel BVI [Code: '.$code.'] [Result: '.$result.']');
		}
	}

}