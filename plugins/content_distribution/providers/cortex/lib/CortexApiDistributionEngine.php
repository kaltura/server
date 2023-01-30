<?php
/**
 * @package plugins.CortexApiDistribution
 * @subpackage lib
 */
class CortexApiDistributionEngine extends DistributionEngine implements
	IDistributionEngineSubmit,
	IDistributionEngineDelete
{
	const CORTEX_API_LOGIN = 'API/Authentication/v1.0/Login';
	const CORTEX_API_SEND_METADATA = 'API/v2.2/DataTable/Documents.Video.General-Library-Video:Update';
	const CORTEX_API_UPLOAD_NEW_MEDIA = 'API/UploadMedia/v3.0/UploadNewMedia';
	const CORTEX_API_UPLOAD_CAPTIONS = 'webapi/mediafile/captions/420_v1';
	const CORTEX_API_GET_METADATA = 'API/v2.2/DataTable/Documents.Video.General-Library-Video:Read';
	const CORTEX_URL_ASSET_FIELD_VALUE = 'asset-management/[RECORD_ID]?WS=AssetManagement”';
	const CORTEX_KALTURA_METADATA_FIELD_ID = 'CoreFieldUniqueidentifier';
	const CORTEX_KALTURA_METADATA_FIELD_CREATED_DATE = 'CoreFieldCreateDate';
	const CORTEX_KALTURA_METADATA_FIELD_RECORD_ID = 'RecordID';
	const CORTEX_KALTURA_METADATA_FIELD_PRESENTERS = 'PresentersPersonsPictured';
	const CORTEX_KALTURA_METADATA_FIELD_JOB_NUMBER = 'JobNumber';
	/**
	 * @var string
	 */
	private $token;
	/**
	 * @var KalturaCortexApiDistributionProfile
	 */
	private $distributionProfile;
	/**
	 * @var string
	 */
	private $cortexSystemId;
	/**
	 * @var string
	 */
	private $recordId;
	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return bool
	 * @throws Exception
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!($data->distributionProfile instanceof KalturaCortexApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaCortexApiDistributionProfile");

		return $this->doSubmit($data, $data->distributionProfile);
	}
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	*/
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		return true;
	}
	/**
	 * @param KalturaDistributionSubmitJobData    $data
	 * @param KalturaCortexApiDistributionProfile $distributionProfile
	 * @return bool
	 * @throws Exception
	 */
	protected function doSubmit(KalturaDistributionSubmitJobData $data, KalturaCortexApiDistributionProfile $distributionProfile)
	{
		if(!$data->providerData instanceof KalturaCortexApiDistributionJobProviderData)
		{
			$this->throwError("Cortex issue: provider data is not KalturaCortexApiDistributionJobProviderData");
		}
		$this->setDistributionProfile($distributionProfile);
		$this->authorizeCortexAccount();
		$result = $this->uploadVideo($data->providerData);
		sleep(30);
		if (kFile::checkFileExists($data->providerData->thumbAssetFilePath))
		{
			$this->uploadThumbnail($data->providerData->thumbAssetFilePath);
		}
		$this->submitMetadata($data->providerData);
		if(isset($data->providerData->captionsInfo))
		{
			foreach ($data->providerData->captionsInfo as $captionInfo)
			{
				/* @var $captionInfo KalturaCortexApiCaptionDistributionInfo */
				$this->uploadCaption($captionInfo);
			}
		}
		if(!empty($this->getDistributionProfile()->metadataprofileid))
		{
			$this->updateCustomMetadata($data->providerData);
		}
		return $result;
	}
	/**
	 * @param KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData
	 * @return void
	 * @throws Exception
	 */
	private function updateCustomMetadata(KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData)
	{
		try{
			if(empty($this->getDistributionProfile()->metadataprofileid))
			{
				return;
			}
			$result = $this->getMetadataFromCortex();
			if(!$result)
			{
				$this->throwError("Failed to get metadata from Cortex");
			}
		}
		catch(Exception $exception)
		{
			return;
		}
		/** @var MetadataPlugin $metadataPlugin */
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$fieldValues = unserialize($apiDistributionJobProviderData->fieldValues);
		try{
			$metadataPlugin->metadata->add($this->getDistributionProfile()->metadataprofileid, KalturaMetadataObjectType::ENTRY, $fieldValues[CortexApiDistributionField::MEDIA_ID], $this->getMetadataXMLByResult($result));
		}
		catch(Exception $e)
		{
			if($e->getCode() == 'METADATA_ALREADY_EXISTS')
			{
				$metadataPlugin->metadata->delete($e->getArgument('ID'));
				$metadataPlugin->metadata->add($this->getDistributionProfile()->metadataprofileid, KalturaMetadataObjectType::ENTRY, $fieldValues[CortexApiDistributionField::MEDIA_ID], $this->getMetadataXMLByResult($result));
			}
		}
	}
	/**
	 * @param string $entryId
	 * @return string[]
	 * @throws Exception
	 */
	private function getMetadataFields(string $entryId)
	{
		$result = array(self::CORTEX_KALTURA_METADATA_FIELD_PRESENTERS => '', self::CORTEX_KALTURA_METADATA_FIELD_JOB_NUMBER => '');
		if(empty($this->getDistributionProfile()->metadataprofileidpushing))
		{
			return $result;
		}
		/** @var MetadataPlugin $metadataPlugin */
		$metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
		$metadataFilter = new KalturaMetadataFilter();
		$metadataFilter->objectIdEqual = $entryId;
		$metadataFilter->metadataProfileIdEqual = $this->getDistributionProfile()->metadataprofileidpushing;
		$metadataFilter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
		$metadataPager = new KalturaFilterPager();
		$metadataPager->pageSize = 1;
		/** @var KalturaMetadataListResponse $metadataListResponse */
		try{
			$metadataListResponse = $metadataPlugin->metadata->listAction($metadataFilter, $metadataPager);
		}
		catch(Exception $e)
		{
			$this->throwError("Cant do metadata.list for entry $entryId, Msg:".$e->getMessage());
		}
		if($metadataListResponse->totalCount == 0)
		{
			return $result;
		}
		$result[self::CORTEX_KALTURA_METADATA_FIELD_JOB_NUMBER] = $this->findMetadataValue($metadataListResponse->objects, self::CORTEX_KALTURA_METADATA_FIELD_JOB_NUMBER);
		$result[self::CORTEX_KALTURA_METADATA_FIELD_PRESENTERS] = $this->findMetadataValue($metadataListResponse->objects, self::CORTEX_KALTURA_METADATA_FIELD_PRESENTERS);
		return $result;
	}
	/**
	 * @param SimpleXMLElement $result
	 * @return string
	 */
	private function getMetadataXMLByResult(SimpleXMLElement $result)
	{
		$createdAtField = self::CORTEX_KALTURA_METADATA_FIELD_CREATED_DATE;
		$idField = self::CORTEX_KALTURA_METADATA_FIELD_ID;
		$recordIdField = self::CORTEX_KALTURA_METADATA_FIELD_RECORD_ID;
		$createdAtValue = $result->{"CoreField.CreateDate"} ?? '';
		$idValue = $result->{"CoreField.Unique-identifier"} ?? '';
		$recordId = $result->{"RecordID"} ??  '';
		$url = $this->getDistributionProfile()->host.'/'.str_replace('[RECORD_ID]', $recordId, self::CORTEX_URL_ASSET_FIELD_VALUE);
		$recordValue = $recordId ? $url : '';
		return "<metadata><$idField>$idValue</$idField><$createdAtField>$createdAtValue</$createdAtField><$recordIdField>$recordValue</$recordIdField></metadata>";
	}
	/**
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	private function getMetadataFromCortex()
	{
		$params = array(
			'CoreField.unique-identifier' => $this->getCortexSystemId()
		);
		return $this->requestCortex($params, self::CORTEX_API_GET_METADATA);
	}
	/**
	 * @return void
	 * @throws Exception
	 */
	private function authorizeCortexAccount()
	{
		$params = array(
			'Login' => $this->getDistributionProfile()->username,
			'Password' => $this->getDistributionProfile()->password,
		);
		$response = $this->requestCortex($params, self::CORTEX_API_LOGIN, true, 202);
		if(!isset($response->Token))
		{
			$this->throwError("HTTP request failed: no token found in response");
		}
		$this->setToken((string)$response->Token);
	}

	/**
	 * @param array $params
	 * @param string     $apiName
	 * @param bool  $post
	 * @param int   $httpSuccessCode
	 * @param array $headers
	 * @param bool  $xmlResponse
	 * @return SimpleXMLElement|string
	 * @throws Exception
	 */
	private function requestCortex(array $params, string $apiName, bool $post = false, int $httpSuccessCode = 200, array $headers = array(), bool $xmlResponse = true)
	{
		$data = array(
			'params' => $params,
			'apiName' => $apiName
		);
		$params['Token'] = $this->getToken();
		KalturaLog::info('Sending data to Cortex, data:'.json_encode($data));
		$url = $this->getDistributionProfile()->host.'/'.$apiName;
		$ch = curl_init();
		if($post === true)
		{
			curl_setopt($ch, CURLOPT_POST,1);
			if(!empty($headers))
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			else
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
		}
		else{
			$url = $url.'?'.http_build_query($params);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		if(($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) != $httpSuccessCode)
		{
			curl_close($ch);
			$this->throwError("Cortex request failed: Http code is not 200 (URL: $url), httpCode:".$httpCode);
		}
		if(!$result)
		{
			$curlError = curl_error($ch);
			$curlErrorNumber = curl_errno($ch);
			curl_close($ch);
			$this->throwError("Cortex request failed: $curlError($curlErrorNumber)");

		}
		curl_close($ch);
		if($xmlResponse === true)
		{
			return $this->getResponseFromXml(simplexml_load_string($result));
		}
		return $result;
	}
	/**
	 * @return string
	 * @throws Exception
	 */
	private function authorizeCortexAccountWithCookie()
	{
		$params = array(
			'Login' => $this->getDistributionProfile()->username,
			'Password' => $this->getDistributionProfile()->password,
		);
		KalturaLog::info('authorizeCaptionCortexAccount, params:'.json_encode($params));

		$url = $this->getDistributionProfile()->host.'/'.self::CORTEX_API_LOGIN;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array());
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$result = curl_exec($ch);
		if(($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) != 202)
		{
			curl_close($ch);
			$this->throwError("Cortex auth failed: Http code is not 202 (URL: $url), httpCode:".$httpCode);
		}
		preg_match("/\<Token\>.*\<\/Token\>/", $result, $matches);
		if(!isset($matches[0]))
		{
			$this->throwError("HTTP request failed: no token found in response");
		}
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		$cookieStr = '';
		if(!empty($matches[1]))
		{
			foreach($matches[1] as $item) {
				$cookieStr .= $item . ";";
			}
		}
		curl_close($ch);
		return $cookieStr;
	}
	/**
	 * @param SimpleXMLElement $xmlLoad
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	private function getResponseFromXml(SimpleXMLElement $xmlLoad)
	{
		if(isset($xmlLoad->APIResponse->Code))
		{
			if((string)$xmlLoad->APIResponse->Code != 'SUCCESS')
			{
				$this->throwError("Response from Cortex is failure, Code:".$xmlLoad->APIResponse->Code);
			}
		}
		elseif(isset($xmlLoad->Response->ErrorList->ErrorDetails))
		{
			$this->throwError("Error in Cortex response, Msg:".$xmlLoad->Response->ErrorList->ErrorDetails);
		}
		elseif(isset($xmlLoad->Response->RecordsAffected->Result->Code))
		{
			if((string)$xmlLoad->Response->RecordsAffected->Result->Code != 'SUCCESS')
			{
				$this->throwError("Response from Cortex updating is failure, Code:".$xmlLoad->Response->RecordsAffected->Result->Code);
			}
		}
		elseif(!isset($xmlLoad->Response->Record))
		{
			$this->throwError("Cortex Response unrecognized");
		}
		return $xmlLoad->APIResponse ?? ($xmlLoad->Response->RecordsAffected->Result ?? $xmlLoad->Response->Record);
	}
	/**
	 * @param string $errorMessage
	 * @throws Exception
	 */
	private function throwError(string $errorMessage)
	{
		KalturaLog::err($errorMessage);
		throw new Exception($errorMessage);
	}
	/**
	 * @param KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData
	 * @return void
	 * @throws Exception
	 */
	private function submitMetadata(KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData)
	{
		try{
			$fieldValues = unserialize($apiDistributionJobProviderData->fieldValues);
			$metadataFields = $this->getMetadataFields($fieldValues[CortexApiDistributionField::MEDIA_ID]);
			$metadata = array();
			$metadata["CoreField.Identifier"] = $this->getCortexSystemId();
			$metadata["CoreField.title:"] = $fieldValues[CortexApiDistributionField::MEDIA_TITLE] ?? '';
			$metadata["CoreField.description:"] = $fieldValues[CortexApiDistributionField::MEDIA_DESCRIPTION] ?? '';
			$metadata["MAY.Kaltura-Record-ID:"] = $fieldValues[CortexApiDistributionField::MEDIA_ID];
			$metadata["MAY.Playback-URL:"] = $apiDistributionJobProviderData->videoFlavorDownloadUrl;//
			$metadata["MAY.Ticket-Contact-or-Requestor:"] = $fieldValues[CortexApiDistributionField::MEDIA_USER_ID] ?? '';
			$metadata["MAY.Person(s)-Present:"] = $metadataFields[self::CORTEX_KALTURA_METADATA_FIELD_PRESENTERS];
			$metadata["CoreField.Creation-Date:"] = $fieldValues[CortexApiDistributionField::MEDIA_CREATION_DATE] ? date('Y-m-d', $fieldValues[CortexApiDistributionField::MEDIA_CREATION_DATE]) : '';
			$metadata["MAY.Legacy-Keywords:"] = $fieldValues[CortexApiDistributionField::MEDIA_KEYWORDS] ? str_replace(',', ', ', $fieldValues[CortexApiDistributionField::MEDIA_KEYWORDS]) : '';
			$metadata["MAY.Job-Number:"] = $metadataFields[self::CORTEX_KALTURA_METADATA_FIELD_JOB_NUMBER];
			$result = $this->requestCortex($metadata, self::CORTEX_API_SEND_METADATA);
			$this->setRecordId($result->RecordID ?? '');
		}
		catch(Exception $e){}
	}
	/**
	 * @param KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData
	 * @return bool
	 * @throws Exception
	 */
	private function uploadVideo(KalturaCortexApiDistributionJobProviderData $apiDistributionJobProviderData)
	{
		try{
			$SystemIdentifier = $this->mediaUpload($apiDistributionJobProviderData->videoAssetFilePath);
			KalturaLog::info("Cortex: upload video succeeded, SystemIdentifier: $SystemIdentifier");
			$this->setCortexSystemId($SystemIdentifier);
			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	/**
	 * @param string $thumbAssetFilePath
	 * @throws Exception
	 */
	private function uploadThumbnail($thumbAssetFilePath)
	{
		try{
			$imageSystemId = $this->mediaUpload($thumbAssetFilePath);
			KalturaLog::info("Cortex: upload thumbnail succeeded, ImageIdentifier: $imageSystemId");
			$metadata = array();
			$metadata["CoreField.Identifier"] = $this->getCortexSystemId();
			$metadata["CoreField.Representative_DO:"] = "[DataTable/v2.2/Documents.Image.Default:Read?CoreField.Identifier=$imageSystemId]";
			$this->requestCortex($metadata, self::CORTEX_API_SEND_METADATA);
			KalturaLog::info("Cortex: setting thumbnail succeeded, ImageIdentifier: $imageSystemId, VideoIdentifier:".$this->getCortexSystemId());
		}
		catch(Exception $e)
		{
			KalturaLog::err("Cortex: upload thumbnail failed, Msg: {$e->getMessage()}");
		}
	}
	/**
	 * @param string $assetFilePath
	 * @return string
	 * @throws Exception
	 */
	private function mediaUpload(string $assetFilePath)
	{
		$cFile = curl_file_create($assetFilePath);
		$fileDetails = array(
			'InputStream' => $cFile,
			'FileName' => basename($assetFilePath),
			'FolderRecordID' => $this->getDistributionProfile()->folderrecordid,
			'UploadMode' => 'ProcessFullyInBackground'
		);
		$result =  $this->requestCortex($fileDetails, self::CORTEX_API_UPLOAD_NEW_MEDIA, true);
		if(empty((string) $result->SystemIdentifier))
		{
			$this->throwError("no SystemIdentifier recieved from Cortex");
		}
		return (string) $result->SystemIdentifier;
	}

	/**
	 * @param KalturaCortexApiCaptionDistributionInfo $captionInfo
	 * @return void
	 * @throws Exception
	 */
	private function uploadCaption(KalturaCortexApiCaptionDistributionInfo $captionInfo)
	{
		if(empty($this->getRecordId()))
		{
			$this->throwError("Cortex recordId not recieved, cant upload captions asset id:".$captionInfo->assetId);
		}
		$captionAssetId = $captionInfo->assetId;
		KalturaLog::info("Cortex: retrieve caption assets content for captionAssetId: [$captionAssetId]");
		try
		{
			if (!class_exists('CaptionPlugin') || !class_exists('KalturaCaptionClientPlugin') || !KalturaPluginManager::getPluginInstance(CaptionPlugin::getPluginName()))
			{
				$this->throwError("Cortex issue: caption plugin disabled");
			}
			$captionClientPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
			$captionAssetContentUrl= $captionClientPlugin->captionAsset->serve($captionAssetId);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$captionAssetContentUrl");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$captionAssetContent = curl_exec($ch);
			curl_close($ch);
		}
		catch(Exception $e)
		{
			$this->throwError("Cortex issue: can't serve caption asset id [$captionAssetId] " . $e->getMessage());
		}
		$params = array(
			'label' => $captionInfo->label,
			'format' => $captionInfo->fileExt,
			'content' => addslashes($captionAssetContent)
		);
		try{
			$cookiesStr = $this->authorizeCortexAccountWithCookie();
			$this->requestCortex($params, self::CORTEX_API_UPLOAD_CAPTIONS.'/'.$this->getRecordId(), true, 200, array('Content-Type: application/json', 'Cookie: '.$cookiesStr), false);
		}
		catch(Exception $e){}

	}

	/**
	 * @return string
	 */
	private function getToken()
	{
		return $this->token;
	}

	/**
	 * @param string $token
	 */
	private function setToken(string $token)
	{
		$this->token = $token;
	}

	/**
	 * @return KalturaCortexApiDistributionProfile
	 */
	private function getDistributionProfile()
	{
		return $this->distributionProfile;
	}

	/**
	 * @param KalturaCortexApiDistributionProfile $distributionProfile
	 */
	private function setDistributionProfile(KalturaCortexApiDistributionProfile $distributionProfile)
	{
		$this->distributionProfile = $distributionProfile;
	}

	/**
	 * @return string
	 */
	private function getCortexSystemId()
	{
		return $this->cortexSystemId;
	}

	/**
	 * @param string $cortexSystemId
	 */
	private function setCortexSystemId(string $cortexSystemId)
	{
		$this->cortexSystemId = $cortexSystemId;
	}

	/**
	 * @return string
	 */
	private function getRecordId()
	{
		return $this->recordId;
	}

	/**
	 * @param string $recordId
	 */
	private function setRecordId(string $recordId)
	{
		$this->recordId = $recordId;
	}
}
