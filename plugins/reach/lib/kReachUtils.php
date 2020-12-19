<?php
/**
 * @package plugins.reach
 */
class kReachUtils
{
	static private $catalogItemDateFields = array('createdAt', 'updatedAt');
	static private $catalogItemTranslateableFields = array('status','serviceType','serviceFeature','turnAroundTime','outputFormat');

	static private $entryVendorTaskDateFields = array('createdAt', 'expectedFinishTime');
	static private $entryVendorTaskTranslateableFields = array('status','serviceType','serviceFeature','turnAroundTime');

	static private $enumTranslateInterface = array(
		'statusEntryVendorTaskEnumTranslate' => 'EntryVendorTaskStatus',
		'statusCatalogItemEnumTranslate'	=> 'VendorCatalogItemStatus',
		'outputFormatEnumTranslate'	=> 'VendorCatalogItemOutputFormat',
		'turnAroundTimeEnumTranslate'	=> 'VendorServiceTurnAroundTime',
		'serviceFeatureEnumTranslate'	=> 'VendorServiceFeature',
		'serviceTypeEnumTranslate'	=> 'VendorServiceType'
	);

	/**
	 * @param $entryId
	 * @param $shouldModerateOutput
	 * @param $turnaroundTime
	 * @return string
	 * @throws Exception
	 */
	public static function generateReachVendorKs($entryId, $shouldModerateOutput = false, $turnaroundTime = dateUtils::DAY, $disableDefaultEntryFilter = false)
	{
		$entry = $disableDefaultEntryFilter ? entryPeer::retrieveByPKNoFilter($entryId) : entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new Exception("Entry Id [$entryId] not Found to create REACH Vendor limited session");

		$partner = $entry->getPartner();

		// Limit the KS to edit access a specific entry
		$privileges = kSessionBase::PRIVILEGE_EDIT . ':' . $entryId;

		// Limit the KS to use only the Vendor Role
		$privileges .= ',' . kSessionBase::PRIVILEGE_SET_ROLE . ':' . UserRoleId::REACH_VENDOR_ROLE;

		// Disable entitlement to avoid entitlement validation when accessing an entry
		$privileges .= ',' . kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY. ':' . $entryId;
		
		$privileges .= ',' . kSessionBase::PRIVILEGE_VIEW . ':' . $entryId;
		
		$privileges .= ',' . kSessionBase::PRIVILEGE_DOWNLOAD . ':' . $entryId;

		if($shouldModerateOutput)
			$privileges .= ',' . kSessionBase::PRIVILEGE_ENABLE_CAPTION_MODERATION;

		$limitedKs = '';
		$result = kSessionUtils::startKSession($partner->getId(), $partner->getSecret(), '', $limitedKs, $turnaroundTime, kSessionBase::SESSION_TYPE_USER, '', $privileges, null, null, false);
		if ($result < 0)
			throw new Exception('Failed to create REACH Vendor limited session for partner '.$partner->getId());

		return $limitedKs;
	}
	
	public static function calcPricePerSecond(entry $entry, $pricePerUnit)
	{
		return ceil($entry->getLengthInMsecs()/1000) * $pricePerUnit;
	}

	public static function calcPricePerMinute(entry $entry, $pricePerUnit)
	{
		return ceil($entry->getLengthInMsecs()/1000/dateUtils::MINUTE) * $pricePerUnit;
	}
	
	public static function calculateTaskPrice(entry $entry, VendorCatalogItem $vendorCatalogItem)
	{
		return $vendorCatalogItem->calculatePriceForEntry($entry);
	}
	
	/**
	 * @param $entry
	 * @param $catalogItem
	 * @param $reachProfile
	 * @return bool
	 */
	public static function isEnoughCreditLeft($entry, VendorCatalogItem $catalogItem, ReachProfile $reachProfile)
	{
		$creditUsed = $reachProfile->getUsedCredit();
		$allowedCredit = $reachProfile->getCredit()->getCurrentCredit();
		if ($allowedCredit == ReachProfileCreditValues::UNLIMITED_CREDIT )
		{
			return true;
		}

		$entryTaskPrice = self::calculateTaskPrice($entry, $catalogItem);
		
		return self::isOrderAllowed($allowedCredit, $creditUsed, $entryTaskPrice);
	}

	/**
	 * @param $entry
	 * @param $catalogItem
	 * @param $reachProfile
	 * @return bool
	 */
	public static function hasCreditExpired(ReachProfile $reachProfile)
	{
		if ($reachProfile->shouldSyncCredit())
		{
			$reachProfile->syncCredit();
			$reachProfile->save();
		}

		$credit = $reachProfile->getCredit();
		return !$credit->isActive();
	}
	
	/**
	 * @param EntryVendorTask $entryVendorTask
	 * @return bool
	 */
	public static function checkCreditForApproval(EntryVendorTask $entryVendorTask)
	{
		$reachProfile = $entryVendorTask->getReachProfile();

		$allowedCredit = $reachProfile->getCredit()->getCurrentCredit();
		if ($allowedCredit == ReachProfileCreditValues::UNLIMITED_CREDIT )
		{
			return true;
		}

		$creditUsed = $reachProfile->getUsedCredit();
		$entryTaskPrice = $entryVendorTask->getPrice();
		
		return self::isOrderAllowed($allowedCredit, $creditUsed, $entryTaskPrice);
	}
	
	public static function isOrderAllowed($allowedCredit, $creditUsed, $entryTaskPrice)
	{
		//If task price is 0 there is no reason to check remaining credit
		//This will allow jobs to run also in cases that due to race condition the used credit is larger than allowed credit
		if($entryTaskPrice == 0)
		{
			return true;
		}
		
		KalturaLog::debug("allowedCredit [$allowedCredit] creditUsed [$creditUsed] entryTaskPrice [$entryTaskPrice]");
		$remainingCredit = $allowedCredit - ($creditUsed  + $entryTaskPrice);
		
		return $remainingCredit >= 0 ? true : false;
	}
	
	public static function checkPriceAddon($entryVendorTask, $taskPriceDiff)
	{
		$reachProfile = $entryVendorTask->getReachProfile();
		$allowedCredit = $reachProfile->getCredit()->getCurrentCredit();

		if ($allowedCredit == ReachProfileCreditValues::UNLIMITED_CREDIT )
			return true;

		$creditUsed = $reachProfile->getUsedCredit();

		KalturaLog::debug("allowedCredit [$allowedCredit] creditUsed [$creditUsed] taskPriceDiff [$taskPriceDiff]");
		$remainingCredit = $allowedCredit - ($creditUsed  + $taskPriceDiff);
		return $remainingCredit >= 0 ? true : false;
	}
	

	public static function isEntryTypeSupported($type, $mediaType = null)
	{
		$supportedTypes = KalturaPluginManager::getExtendedTypes(entryPeer::OM_CLASS, entryType::MEDIA_CLIP);
		$supported = in_array($type, $supportedTypes);
		if($mediaType && $supported)
		{
			$supported = $supported && in_array($mediaType, array(entry::ENTRY_MEDIA_TYPE_VIDEO,entry::ENTRY_MEDIA_TYPE_AUDIO));
		}
		
		return $supported;
		
	}

	public static function reachStrToTime($offset , $value)
	{
		$original = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$result = strtotime($offset, $value);
		date_default_timezone_set($original);
		return $result;
	}

	/**
	 * @return array
	 */
	public static function getEntryVendorTaskCsvHeaders()
	{
		return array('id', 'partnerId', 'vendorPartnerId', 'entryId', 'createdAt', 'serviceType', 'serviceFeature', 'turnAroundTime', 'expectedFinishTime', 'status', 'errDescription');
	}

	/**
	 * @return array
	 */
	public static function getVendorCatalogItemsCsvHeaders()
	{
		return array('id','status','vendorPartnerId','name','systemName','serviceFeature','serviceType','turnAroundTime','sourceLanguage','targetLanguage','outputFormat','createdAt','updatedAt','enableSpeakerId','fixedPriceAddons','pricing:pricePerUnit','pricing:priceFunction', 'flavorParamsId', 'clearAudioFlavorParamsId','allowResubmission');
	}


	protected static function getTranslationFunctionsByType($type)
	{
		switch ($type)
		{
			case 'vendorCatalogItem':
				return array('catalogItemDateFields', 'catalogItemTranslateableFields', 'getVendorCatalogItemsCsvHeaders');

			case 'entryVendorTask':
				return array('entryVendorTaskDateFields', 'entryVendorTaskTranslateableFields', 'getEntryVendorTaskCsvHeaders');
		}
	}

	protected static function getTranslatableFieldByType($type, $translateableField)
	{
		if ($translateableField === 'status')
		{
			switch ($type)
			{
				case 'vendorCatalogItem':
					return 'statusCatalogItem';

				case 'entryVendorTask':
					return 'statusEntryVendorTask';
			}
		}

		return $translateableField;
	}

	/**
	 * @param $values
	 * @param string $type
	 * @return string
	 */
	public static function createCsvRowData($values, $type)
	{
		list($dateFields, $translateableFields, $getCsvHeaders) = self::getTranslationFunctionsByType($type);

		$csvData = array();
		foreach (self::$$dateFields as $dateField)
		{
			if (isset($values[$dateField]))
			{
				$values[$dateField] = self::getHumanReadbaleDate($values[$dateField]);
			}
		}

		foreach (self::$$translateableFields as $translateableField)
		{
			if (isset($values[$translateableField]))
			{
				$translateableFieldName = self::getTranslatableFieldByType($type, $translateableField);
				$values[$translateableField] = self::translateEnumsToHumanReadable($translateableFieldName, $values[$translateableField]);
			}
		}

		foreach (self::$getCsvHeaders() as $field)
		{
			if (isset($values[$field]))
			{
				$csvData[$field] = $values[$field];
			}
			else
			{
				$csvData[$field] = 'N\A';
			}
		}
		$csvData = KCsvWrapper::validateCsvFields($csvData);
		return implode(',',$csvData);
	}

	/**
	 * @param $object
	 * @return array|null
	 */
	public static function getObejctValues($object)
	{
		if (!$object)
		{
			return null;
		}
		$values = get_object_vars($object);
		$additionalValues = array();
		foreach ($values as $key => $value)
		{
			if (is_object($value))
			{
				$objectValues = self::getObejctValues($value);
				foreach ($objectValues as $innerKey => $innerValue)
				{
					$additionalValues[$key . ':' . $innerKey] = $innerValue;
				}
				unset($values[$key]);
			}
		}
		return array_merge($values,$additionalValues);
	}

	/**
	 * @param $enumName
	 * @param $enumValue
	 * @return string
	 */
	protected static function translateEnumsToHumanReadable($enumName, $enumValue)
	{
		$interfaceConstants = self::$enumTranslateInterface[$enumName . "EnumTranslate"];
		if (!isset($interfaceConstants))
		{
			return 'N\A';
		}

		$iClass = new ReflectionClass($interfaceConstants);
		$constants = array_flip($iClass->getConstants());
		if (!isset($constants[$enumValue]))
		{
			return 'N\A';
		}

		return $constants[$enumValue];
	}

	/**
	 * @param $unixTimeStamp
	 * @return false|string
	 */
	protected static function getHumanReadbaleDate($unixTimeStamp)
	{
		if (!$unixTimeStamp)
		{
			return 'N\A';
		}

		return date("Y-m-d H:i", $unixTimeStamp);
	}

	public static function setSelectedRelativeTime($filterDateInput, $filter)
	{
		$startTime = 0;
		$endTime = 0;
		if (!preg_match('/^(\-|\+)(\d+$)/', $filterDateInput, $matches))
		{
			return;
		}
		$sign = $matches[1];
		$hours = (int)$matches[2];
		$timeFromHourToSec = $hours * 60 * 60;
		if ($sign === '-')
		{
			$startTime = time() - $timeFromHourToSec;
			$endTime = time();
		}
		else if ($sign === '+')
		{
			$startTime = time();
			$endTime = time() + $timeFromHourToSec;
		}

		$filter->expectedFinishTimeGreaterThanOrEqual = $startTime;
		$filter->expectedFinishTimeLessThanOrEqual = $endTime;
	}
}