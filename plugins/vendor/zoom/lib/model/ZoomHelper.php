<?php

/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class ZoomHelper
{
	const VENDOR_MAP = 'vendor';
	const ZOOM_ACCOUNT_PARAM = 'ZoomAccount';
	/** payload data */
	const ACCOUNT_ID = "account_id";
	const PAYLOAD = 'payload';

	/** php body */
	const PHP_INPUT = 'php://input';
	const SUFFIX_ZOOM = '.zoom';
	const ORDER_RECORDING_TYPE =  array(
		'shared_screen_with_speaker_view(CC)',
		'shared_screen_with_speaker_view',
		'shared_screen_with_gallery_view',
		'shared_screen',
		'speaker_view',
		'active_speaker',
		'gallery_view',
		'audio_only',
		'audio_transcript',
		'closed_caption',
		'chat_file',
		'poll'
	);
	
	const RECORDING_FILE_STATUS = 'status';
	const RECORDING_FILE_STATUS_PROCESSING = 'processing';
	
	/* @var zoomVendorIntegration $zoomIntegration */
	protected static $zoomIntegration;

	/**
	 * @param $accountId
	 * @param bool $includeDeleted
	 * @param $partnerId
	 * @return null|zoomVendorIntegration
	 * @throws PropelException
	 */
	public static function getZoomIntegrationByAccountId($accountId, $includeDeleted = false, $partnerId = null)
	{
		if($includeDeleted)
		{
			self::$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartnerNoFilter($accountId, VendorTypeEnum::ZOOM_ACCOUNT, $partnerId);
		}
		else
		{
			self::$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT, $partnerId);
		}

		return self::$zoomIntegration;
	}

	/**
	 * @return zoomVendorIntegration
	 */
	public static function getZoomIntegration()
	{
		self::verifyZoomIntegration(self::$zoomIntegration);
		return self::$zoomIntegration;
	}

	public static function setZoomIntegration($zoomIntegration)
	{
		self::verifyZoomIntegration($zoomIntegration);
		self::$zoomIntegration = $zoomIntegration;
	}

	public static function exitWithError($errMsg)
	{
		KalturaLog::err($errMsg);
		if(self::$zoomIntegration)
		{
			self::$zoomIntegration->saveLastError($errMsg);
		}

		KExternalErrors::dieGracefully();
	}

	/**
	 * @param array $zoomUserPermissions
	 * @return bool
	 */
	public static function canConfigureEventSubscription($zoomUserPermissions)
    {
		if(is_array($zoomUserPermissions))
		{
			return in_array('Recording:Read', $zoomUserPermissions) && in_array('Recording:Edit', $zoomUserPermissions);
		}
	    self::exitWithError(kZoomErrorMessages::ERROR_WHILE_RETRIEVING_USER_PERMISSIONS);
    }

    /**
     * @param array $tokens
     * @param array $zoomConfiguration
     * @throws Exception
     */
	public static function loadLoginPage($tokens, $zoomConfiguration)
	{
		$file_path = dirname(__FILE__) . '/../api/webPage/kalturaZoomLoginPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens);
			$secretToken = kZoomOauth::getTokenForEncryption($zoomConfiguration);
			list($enc, $iv) = AESEncrypt::encrypt($secretToken, $tokensString);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			$page = str_replace('@encryptData@', base64_encode($enc), $page);
			$page = str_replace('@iv@', base64_encode($iv), $page);
			echo $page;
			die();
		}
	}
	
	/**
	 * @param $authCode
	 * @throws Exception
	 */
	public static function loadRegionalCloudRedirectionPage($authCode)
	{
		$file_path = dirname(__FILE__) . '/../api/webPage/kalturaRegionalRedirectPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$page = str_replace('@authCode@', $authCode, $page);
			
			echo $page;
			die();
		}
		
		throw new KalturaAPIException('unable to find regional redirect page, please contact support');
	}

	/**
	 * @param $data
	 * @return string
	 */
	public static function extractAccountIdFromDeAuthPayload($data)
	{
		$payload = $data[self::PAYLOAD];
        return $payload[self::ACCOUNT_ID];
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public static function getPayloadData($plain=false)
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		if($plain)
		{
			return $request_body;
		}
		return json_decode($request_body, true);
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
     */
	public static function verifyZoomIntegration($zoomIntegration)
	{
		if (!$zoomIntegration)
		{
			self::exitWithError(kZoomErrorMessages::NO_INTEGRATION_DATA);
		}

		if($zoomIntegration->getStatus() == VendorIntegrationStatus::DISABLED)
		{
			self::exitWithError(kZoomErrorMessages::UPLOAD_DISABLED);
		}
	}
	
	public static function shouldHandleFileType($recordingFileType)
	{
		switch($recordingFileType)
		{
			case 'MP4':
			case 'CHAT':
			case 'TRANSCRIPT':
			case 'CC':
			case 'M4A':
				return true;
			default:
				return false;
		}
	}
	
	public static function shouldHandleFileTypeEnum($recordingFileType)
	{
		switch($recordingFileType)
		{
			case kRecordingFileType::VIDEO:
			case kRecordingFileType::CHAT:
			case kRecordingFileType::TRANSCRIPT:
			case kRecordingFileType::AUDIO:
				return true;
			default:
				return false;
		}
	}
	
	public static function orderRecordingFiles($recordingFiles, $recordingStart, $recordingType, &$fileInStatusProcessingExists)
	{
		$recordingFilesOrdered = array();
		foreach($recordingFiles as $recordingFile)
		{
			if( isset($recordingFile[self::RECORDING_FILE_STATUS]) && ($recordingFile[self::RECORDING_FILE_STATUS] === self::RECORDING_FILE_STATUS_PROCESSING) )
			{
				$fileInStatusProcessingExists = true;
			}
			
			if(!isset($recordingFile[$recordingType]))
			{
				continue;
			}
			if(!isset($recordingFilesOrdered[$recordingFile[$recordingStart]]))
			{
				$recordingFilesOrdered[$recordingFile[$recordingStart]] = array();
			}
			$recordingFilesOrdered[$recordingFile[$recordingStart]][] = $recordingFile;
		}
		ksort($recordingFilesOrdered);
		return self::orderRecordingFilesByRecordingType($recordingFilesOrdered, $recordingType);
	}
	
	public static function orderRecordingFilesByRecordingType($recordingFilesOrdered, $recordingType)
	{
		foreach ($recordingFilesOrdered as $time => $recordingFilesPerTimeSlot)
		{
			$filesOrderByRecordingType = array();
			foreach ($recordingFilesPerTimeSlot as $recordingFile)
			{
				if(!isset($filesOrderByRecordingType[$recordingFile[$recordingType]]))
				{
					$filesOrderByRecordingType[$recordingFile[$recordingType]] = array();
				}
				$filesOrderByRecordingType[$recordingFile[$recordingType]][] = $recordingFile;
			}
			$recordingFilesOrdered[$time] = self::sortArrayByValuesArray($filesOrderByRecordingType, self::ORDER_RECORDING_TYPE);
		}
		return $recordingFilesOrdered;
	}
	
	public static function sortArrayByValuesArray(array $filesOrderByRecordingType, array $orderArray)
    {
		$ordered = array();
		foreach ($orderArray as $item)
		{
			$filesByRecordingType = self::getFilesByRecordingType($filesOrderByRecordingType, $item);
			foreach ($filesByRecordingType as $fileByRecordingType)
			{
				foreach ($fileByRecordingType as $file)
				{
					$ordered[] = $file;
				}
			}
		}
		return $ordered;
	}
	
	public static function getFilesByRecordingType($filesOrderByRecordingType, $item)
	{
		$filesByRecordingType = array();
		foreach ($filesOrderByRecordingType as $recordingType => $value)
		{
			if ($recordingType === $item)
			{
				$filesByRecordingType[$recordingType] = $value;
			}
		}
		return $filesByRecordingType;
	}

	/**
	 * Create categories for integration from a string of comma separated values
	 * @param string $categoryNames
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return array List of created categories
	 * @throws PropelException
	 */
	public static function createCategoriesForIntegration($categoryNames, $zoomIntegration)
	{
		$categoriesList = explode(',', $categoryNames);
		$createdCategories = array();
		foreach ($categoriesList as $category)
		{
			$category = trim($category);
			if (!$category)
			{
				continue;
			}
			if (VendorHelper::createCategoryForVendorIntegration($zoomIntegration->getPartnerId(), $category, $zoomIntegration))
			{
				$createdCategories[] = $category;
			}
		}
		return $createdCategories;
	}
}
