<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class VendorCatalogItem extends BaseVendorCatalogItem implements IRelatedObject 
{

	/**
	 * Initializes internal state of VendorCatalogItem object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
		$this->applyDefaultValues();
	}
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
	}
	
	const CUSTOM_DATA_PRICING = 'pricing';
	const CUSTOM_DATA_BULK_UPLOAD_ID = 'bulkUploadId';
	const CUSTOM_DATA_LAST_BULK_UPDATE_ID = 'lastBulkUpdateId';
	const CUSTOM_DATA_ENGINE_TYPE = 'engineType';
	const CUSTOM_DATA_ALLOW_RESUBMISSION = 'allowResubmission';
	const CUSTOM_DATA_VENDOR_DATA = 'vendorData';
	const CUSTOM_DATA_STAGE = 'stage';
	const CUSTOM_DATA_CONTRACT = 'contract';
	const CUSTOM_DATA_CREATED_BY = 'createdBy';
	const CUSTOM_DATA_NOTES = 'notes';
	const CUSTOM_ADMIN_TAGS_TO_EXCLUDE = 'admin_tags_to_exclude';

	public function setAllowResubmission($allowResubmission)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RESUBMISSION, $allowResubmission);
	}

	/**
	 * @return boolean
	 */
	public function getAllowResubmission()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RESUBMISSION);
	}

	public function setPricing($pricing)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PRICING, serialize($pricing));
	}
	
	/**
	 * @return kCatalogItemPricing
	 */
	public function getPricing()
	{
		$pricing = $this->getFromCustomData(self::CUSTOM_DATA_PRICING);
		
		if($pricing)
			$pricing = unserialize($pricing);
		
		return $pricing;
	}

	public function setBulkUploadId($bulkUploadId)
	{
		$this->putInCustomData(self::CUSTOM_DATA_BULK_UPLOAD_ID ,$bulkUploadId);
	}

	public function getBulkUploadId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_BULK_UPLOAD_ID);
	}

	public function setLastBulkUpdateId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LAST_BULK_UPDATE_ID ,$v);
	}

	public function getLastBulkUpdateId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LAST_BULK_UPDATE_ID);
	}

	public function setEngineType($engineType)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENGINE_TYPE, $engineType);
	}

	public function getEngineType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENGINE_TYPE);
	}

	public function setVendorData($vendorData)
	{
		$this->putInCustomData(self::CUSTOM_DATA_VENDOR_DATA, $vendorData);
	}

	/**
	 * @return string
	 */
	public function getVendorData()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_VENDOR_DATA);
	}

	
	public function getPartnerId()
	{
		return 0;
	}

	protected function getPrivileges($entryId, $shouldModerateOutput)
	{
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

		return $privileges;
	}

	protected function getPuserId($entry)
	{
		return '';
	}

	/**
	 * @param $entryId
	 * @param $shouldModerateOutput
	 * @param $turnaroundTime
	 * @return string
	 * @throws Exception
	 */
	public function generateReachVendorKs($entryId, $shouldModerateOutput = false, $turnaroundTime = dateUtils::DAY, $disableDefaultEntryFilter = false)
	{
		$entry = $disableDefaultEntryFilter ? entryPeer::retrieveByPKNoFilter($entryId) : entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new Exception("Entry Id [$entryId] not Found to create REACH Vendor limited session");

		$partner = $entry->getPartner();
		$privileges = $this->getPrivileges($entryId, $shouldModerateOutput);
		$puser_id = $this->getPuserId($entry);

		$limitedKs = '';
		$result = kSessionUtils::startKSession($partner->getId(), $partner->getSecret(), $puser_id, $limitedKs, $turnaroundTime, kSessionBase::SESSION_TYPE_USER, '', $privileges, null, null, false);
		if ($result < 0)
			throw new Exception('Failed to create REACH Vendor limited session for partner '.$partner->getId());

		return $limitedKs;
	}
	
	public function getKsExpiry()
	{
		$ksExpiry = $this->getTurnAroundTime() * 2;
		
		//Minimum KS expiry should be set 7 days
		return max($ksExpiry, dateUtils::DAY * 7);
	}
	
	public function calculatePriceForEntry(entry $entry, $taskDuration = null)
	{
		$durationMsec = $taskDuration ? $taskDuration : $entry->getLengthInMsecs();
		return call_user_func($this->getPricing()->getPriceFunction(), $durationMsec, $this->getPricing()->getPricePerUnit());
	}
	
	public function getTaskVersion($entryId, $jobData = null)
	{
		$sourceFlavor = assetPeer::retrieveOriginalByEntryId($entryId);
		return $sourceFlavor != null ? $sourceFlavor->getVersion() : 0;
	}

	public static function translateServiceFeatureEnum($catalogItemId)
	{
		$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($catalogItemId);
		if (!$vendorCatalogItem)
		{
			return '';
		}

		$reflector = new ReflectionClass('VendorServiceFeature');
		$constantNames = array_flip($reflector->getConstants());
		$serviceFeature = $vendorCatalogItem->getServiceFeature();
		$constantName = '';
		if(isset($constantNames[$serviceFeature]))
		{
			$constantName = str_replace("_", " ", strtolower($constantNames[$serviceFeature]));
		}
		return $constantName;
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("vendorCatalogItem:id=".strtolower($this->getId()));
	}

	public function isDuplicateTask(entry $entry)
	{
		$version = $this->calculateEntryVendorTaskVersion($entry);

		$activeTask = EntryVendorTaskPeer::retrieveOneActiveOrCompleteTask($entry->getId(), $this->getId(), $entry->getPartnerId(), $version);
		if($activeTask)
		{
			return true;
		}

		return false;
	}

	public function calculateEntryVendorTaskVersion ($entry)
	{
		$sourceFlavor = assetPeer::retrieveOriginalByEntryId($entry->getId());

		return !is_null($sourceFlavor) ? $sourceFlavor->getVersion() : 0;
	}

	public function isEntryTypeSupported($type, $mediaType = null)
	{
		$supportedTypes = KalturaPluginManager::getExtendedTypes(entryPeer::OM_CLASS, entryType::MEDIA_CLIP);
		$supported = in_array($type, $supportedTypes);
		if($mediaType && $supported)
		{
			$supported = $supported && in_array($mediaType, array(entry::ENTRY_MEDIA_TYPE_VIDEO,entry::ENTRY_MEDIA_TYPE_AUDIO));
		}

		return $supported;
	}

	public function getTaskJobData($object)
	{
		return null;
	}

	public function requiresEntryReady()
	{
		return true;
	}

	public function setStage($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_STAGE, $v);
	}

	public function getStage()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_STAGE, null, VendorCatalogItemStage::PRODUCTION);
	}

	public function setContract($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CONTRACT, $v);
	}

	public function getContract()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONTRACT);
	}

	public function setCreatedBy($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CREATED_BY, $v);
	}

	public function getCreatedBy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CREATED_BY);
	}

	public function setNotes($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_NOTES, $v);
	}

	public function getNotes()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_NOTES);
	}
	
	public function setAdminTagsToExclude($v)
	{
		$this->putInCustomData(self::CUSTOM_ADMIN_TAGS_TO_EXCLUDE, $v);
	}
	
	public function getAdminTagsToExclude()
	{
		return $this->getFromCustomData(self::CUSTOM_ADMIN_TAGS_TO_EXCLUDE);
	}
	
	public function getAdminTagsToExcludeArray()
	{
		$adminTagsToExclude = $this->getFromCustomData(self::CUSTOM_ADMIN_TAGS_TO_EXCLUDE);
		if(!$adminTagsToExclude)
		{
			return array();
		}
		
		return array_map("trim", explode(',', $adminTagsToExclude));
	}

	public function isEntryDurationExceeding(entry $entry)
	{
		return false;
	}
} // VendorCatalogItem
