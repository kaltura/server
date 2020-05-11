<?php
/**
 * @package plugins.bulkUploadCsv
 */
class BulkUploadCsvPlugin extends KalturaPlugin implements IKalturaBulkUpload, IKalturaPending, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'bulkUploadCsv';

	const FEATURE_CSV_HEADER_ROW = 'FEATURE_CSV_HEADER_ROW';

	const BULKUPLOAD_CSV_FLOW_MANAGER = "kBulkUploadCsvFlowManager";
	/**
	 *
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$drmDependency = new KalturaDependency(BulkUploadPlugin::PLUGIN_NAME);
		
		return array($drmDependency);
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('BulkUploadCsvType');
	
		if($baseEnumName == 'BulkUploadType')
			return array('BulkUploadCsvType');
		
		return array();
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		 //Gets the right job for the engine
		if($baseClass == 'kBulkUploadJobData' && (!$enumValue || $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadCsvType::CSV)))
			return new kBulkUploadCsvJobData();
		
		 //Gets the right job for the engine
		if($baseClass == 'KalturaBulkUploadJobData' && (!$enumValue || $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadCsvType::CSV)))
			return new KalturaBulkUploadCsvJobData();
		
		//Gets the engine (only for clients)
		if($baseClass == 'KBulkUploadEngine' && class_exists('KalturaClient') && (!$enumValue || $enumValue == KalturaBulkUploadType::CSV))
		{
			list($job) = $constructorArgs;
			/* @var $job KalturaBatchJob */
			switch ($job->data->bulkUploadObjectType)
			{
			    case KalturaBulkUploadObjectType::ENTRY:
			        return new BulkUploadEntryEngineCsv($job);
			    case KalturaBulkUploadObjectType::CATEGORY:
			        return new BulkUploadCategoryEngineCsv($job);
			    case KalturaBulkUploadObjectType::USER:
			        return new BulkUploadUserEngineCsv($job);
			    case KalturaBulkUploadObjectType::CATEGORY_USER:
			        return new BulkUploadCategoryUserEngineCsv($job);
			    case KalturaBulkUploadObjectType::CATEGORY_ENTRY:
			        return new BulkUploadCategoryEntryEngineCsv($job);
			    case KalturaBulkUploadObjectType::VENDOR_CATALOG_ITEM:
				return new BulkUploadVendorCatalogItemEngineCsv($job);
			}
			
		}
				
		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}
	
	/**
	 * Returns the correct file extension for bulk upload type
	 * @param int $enumValue code API value
	 */
	public static function getFileExtension($enumValue)
	{
		if(!$enumValue || $enumValue == self::getBulkUploadTypeCoreValue(BulkUploadCsvType::CSV))
			return 'csv';
	}
	
	
	/**
	 * Returns the log file for bulk upload job
	 * @param BatchJob $batchJob bulk upload batchjob
	 */
	public static function writeBulkUploadLogFile($batchJob)
	{
		if($batchJob->getJobSubType() && ($batchJob->getJobSubType() != self::getBulkUploadTypeCoreValue(BulkUploadCsvType::CSV))){
			return;
		}
		
		header("Content-Type: text/plain; charset=UTF-8");

		$criteria = new Criteria();
		$criteria->add(BulkUploadResultPeer::BULK_UPLOAD_JOB_ID, $batchJob->getId());
		$criteria->addAscendingOrderByColumn(BulkUploadResultPeer::LINE_INDEX);
		$criteria->setLimit(100);
		$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
		
		if(!count($bulkUploadResults))
			die("Log file is not ready");
			
		$STDOUT = fopen('php://output', 'w');
		$data = $batchJob->getData();
        /* @var $data kBulkUploadJobData */
		
		//Add header row to the output CSV only if partner level permission for it exists
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		if (PermissionPeer::isValidForPartner(self::FEATURE_CSV_HEADER_ROW, $partnerId))
		{
    		$headerRow = $data->getColumns();
    		$headerRow[] = "resultStatus";
    		$headerRow[] = "objectId";
    		$headerRow[] = "objectStatus";
    		$headerRow[] = "errorDescription";
    		KCsvWrapper::sanitizedFputCsv($STDOUT, $headerRow);
		}
		
		$handledResults = 0;
		while(count($bulkUploadResults))
		{
			$handledResults += count($bulkUploadResults);
			foreach($bulkUploadResults as $bulkUploadResult)
			{
			    /* @var $bulkUploadResult BulkUploadResult */
			    $values = str_getcsv($bulkUploadResult->getRowData());
	//		    switch ($bulkUploadResult->getObjectType())
	//		    {
	//		        case BulkUploadObjectType::ENTRY:
	//		            $values = self::writeEntryBulkUploadResults($bulkUploadResult, $data);
	//		            break;
	//		        case BulkUploadObjectType::CATEGORY:
	//		            $values = self::writeCategoryBulkUploadResults($bulkUploadResult, $data);
	//		            break;
	//		        case BulkUploadObjectType::CATEGORY_USER:
	//		            $values = self::writeCategoryUserBulkUploadResults($bulkUploadResult, $data);
	//		            break;
	//		        case BulkUploadObjectType::USER:
	//		            $values = self::writeUserBulkUploadResults($bulkUploadResult, $data);
	//		            break;
	//		        default:
	//
	//		            break;
	//		    }
				
	            $values[] = $bulkUploadResult->getStatus();
				$values[] = $bulkUploadResult->getObjectId();
				$values[] = $bulkUploadResult->getObjectStatus();
				$values[] = preg_replace('/[\n\r\t]/', ' ', $bulkUploadResult->getErrorDescription());


				KCsvWrapper::sanitizedFputCsv($STDOUT, $values);
			}
			
    		if(count($bulkUploadResults) < $criteria->getLimit())
    			break;
	    		
    		kMemoryManager::clearMemory();
    		$criteria->setOffset($handledResults);
			$bulkUploadResults = BulkUploadResultPeer::doSelect($criteria);
		}
		fclose($STDOUT);
		
		kFile::closeDbConnections();
		exit;
	}
	
	/**
	 * Returns array of column values for the bulk upload result
	 * @param int $bulkUploadObjectType
	 */
	protected static function getHeaderRow ($bulkUploadObjectType, $csvVersion)
	{
	    switch ($bulkUploadObjectType)
	    {
	        case BulkUploadObjectType::ENTRY:
	            $ret = array ("*title", "description", "tags", "url", "contentType",);
	            if ($csvVersion > 1)
    	            array_merge($ret, array ("conversionProfileId", "accessProfileId",
    	                    "category", "scheduleStartDate", "scheduleEndDate", "thumbnailUrl", "partnerData", "creatorId", "entitledUsersEdit", "entitledUsersPublish", "ownerId"));
	            return $ret;
    	        break;
	        case BulkUploadObjectType::CATEGORY:
	            return array ("*name", "relativePath", "tags", "description", "referenceId", "privacy", "appearInList", "contributionPolicy",
	                        "inheritanceType", "userJoinPolicy", "defaultPermissionLevel", "owner", "partnerData", "partnerSortValue", "moderation");
	            break;
	        case BulkUploadObjectType::CATEGORY_USER:
	            return array ("*categoryId", "userId", "categoryReferenceId", "permissionLevel", "updateMethod", "status",);
	            break;
	        case BulkUploadObjectType::USER:
	            return array("*screenName", "email", "dateOfBirth", "country", "state", "city", "zip", "gender", "firstName", "lastName", "isAdmin", "tags", "roleIds", "partnerData",);
	            break;
	    }
	}
	
	/**
	 * Function constructs an array of the return values of the bulk upload result and returns it
	 * @param BulkUploadResultCategory $bulkUploadResult
	 * @param kJobData $data
	 * @return array
	 */
	protected static function writeCategoryBulkUploadResults(BulkUploadResultCategory $bulkUploadResult, kJobData $data)
	{
	    /* @var $bulkUploadResult BulkUploadResultCategory */
	    $values = array();
	    $values[] = $bulkUploadResult->getName();
	    $values[] = $bulkUploadResult->getRelativePath();
	    $values[] = $bulkUploadResult->getTags();
	    $values[] = $bulkUploadResult->getDescription();
	    $values[] = $bulkUploadResult->getReferenceId();
	    $values[] = $bulkUploadResult->getAppearInList();
	    $values[] = $bulkUploadResult->getPrivacy();
	    $values[] = $bulkUploadResult->getContributionPolicy();
	    $values[] = $bulkUploadResult->getInheritance();
	    $values[] = $bulkUploadResult->getUserJoinPolicy();
	    $values[] = $bulkUploadResult->getDefaultPermissionLevel();
	    $values[] = $bulkUploadResult->getOwner();
	    $values[] = $bulkUploadResult->getPartnerData();
	    $values[] = $bulkUploadResult->getPartnerSortValue();
	    $values[] = $bulkUploadResult->getModeration();
	    
	    return $values;
	}
	
    /**
     * Function constructs an array of the return values of the bulk upload result and returns it
     * @param BulkUploadResult $bulkUploadResult
     * @param kJobData $data
     * @return array
     */
    protected static function writeEntryBulkUploadResults(BulkUploadResult $bulkUploadResult, kJobData $data)
	{
        $values = array(
			$bulkUploadResult->getTitle(),
			$bulkUploadResult->getDescription(),
			$bulkUploadResult->getTags(),
			$bulkUploadResult->getUrl(),
			$bulkUploadResult->getContentType(),
		);
			
		if($data->getCsvVersion() > 1)
		{
			$values[] = $bulkUploadResult->getConversionProfileId();
			$values[] = $bulkUploadResult->getAccessControlProfileId();
			$values[] = $bulkUploadResult->getCategory();
			$values[] = $bulkUploadResult->getScheduleStartDate('Y-m-d\TH:i:s');
			$values[] = $bulkUploadResult->getScheduleEndDate('Y-m-d\TH:i:s');
			$values[] = $bulkUploadResult->getThumbnailUrl();
			$values[] = $bulkUploadResult->getPartnerData();
			$values[] = $bulkUploadResult->getCreatorId();
			$values[] = $bulkUploadResult->getEntitledUsersEdit();
			$values[] = $bulkUploadResult->getEntitledUsersPublish();
			$values[] = $bulkUploadResult->getOwnerId();
		}
		
		return $values;
	}
	
    /**
     * Function constructs an array of the return values of the bulk upload result and returns it
     * @param BulkUploadResult $bulkUploadResult
     * @param kJobData $data
     * @return array
     */
    protected static function writeCategoryUserBulkUploadResults(BulkUploadResult $bulkUploadResult, kJobData $data)
	{
	    /* @var $bulkUploadResult BulkUploadResultCategoryKuser */
	    $values = array();
	    $values[] = $bulkUploadResult->getCategoryId();
	    $values[] = $bulkUploadResult->getUserId();
	    $values[] = $bulkUploadResult->getCategoryReferenceId();
	    $values[] = $bulkUploadResult->getPermissionLevel();
	    $values[] = $bulkUploadResult->getUpdateMethod();
	    $values[] = $bulkUploadResult->getRequiredStatus();
	    
	    return $values;
	}
	
    /**
     * Function constructs an array of the return values of the bulk upload result and returns it
     * @param BulkUploadResult $bulkUploadResult
     * @param kJobData $data
     * @return array
     */
    protected static function writeUserBulkUploadResults(BulkUploadResult $bulkUploadResult, kJobData $data)
	{
	    /* @var $bulkUploadResult BulkUploadResultKuser */
	    $values = array();
	    $values[] = $bulkUploadResult->getPuserId();
	    $values[] = $bulkUploadResult->getScreenName();
	    $values[] = $bulkUploadResult->getEmail();
	    $values[] = is_null($bulkUploadResult->getDateOfBirth()) ? '' : date('Y-m-d', $bulkUploadResult->getDateOfBirth());
	    $values[] = $bulkUploadResult->getCountry();
	    $values[] = $bulkUploadResult->getState();
	    $values[] = $bulkUploadResult->getCity();
	    $values[] = $bulkUploadResult->getZip();
	    $values[] = $bulkUploadResult->getGender();
	    $values[] = $bulkUploadResult->getFirstName();
	    $values[] = $bulkUploadResult->getLastName();
	    $values[] = $bulkUploadResult->getTags();
	    $values[] = $bulkUploadResult->getPartnerData();
	    
	    return $values;
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	private static function stringToSafeXml($string)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$safe = kString::xmlEncode($string);
		return $safe;
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBulkUploadTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BulkUploadType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getEventConsumers()
	{
		return array(self::BULKUPLOAD_CSV_FLOW_MANAGER);
	}
}
