<?php
/**
 * @package plugins.reach
 */
class kReachUtils
{
	/**
	 * @param $entryId
	 * @return string
	 * @throws Exception
	 */
	public static function generateReachVendorKs($entryId, $shouldModerate = false)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new Exception("Entry Id [$entryId] not Found to create REACH Vendor limited session");

		$partner = $entry->getPartner();

		// Limit the KS to edit access a specific entry
		$privileges = kSessionBase::PRIVILEGE_EDIT . ':' . $entryId;

		// Limit the KS to use only the Vendor Role
		$privileges .= ',' . kSessionBase::PRIVILEGE_SET_ROLE . ':' . UserRoleId::REACH_VENDOR_ROLE;

		// Disable entitlement to avoid entitlement validation when accessing an entry
		$privileges .= ',' . kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY. ':' . $entryId;

		if($shouldModerate)
			$privileges .= ',' . kSessionBase::PRIVILEGE_ENABLE_CAPTION_MODERATION;

		$limitedKs = '';
		$result = kSessionUtils::startKSession($partner->getId(), $partner->getSecret(), '', $limitedKs, dateUtils::DAY, kSessionBase::SESSION_TYPE_USER, '', $privileges, null, null);
		if ($result < 0)
			throw new Exception('Failed to create REACH Vendor limited session for partner '.$partner->getId());

		return $limitedKs;
	}
	
	public static function calcPricePerSecond(entry $entry, $pricePerUnit)
	{
		return ceil(($entry->getLengthInMsecs()/1000) * $pricePerUnit);
	}

	public static function calcPricePerMinute(entry $entry, $pricePerUnit)
	{
		return ceil(($entry->getLengthInMsecs()/1000/dateUtils::MINUTE) * $pricePerUnit);
	}
	
	public static function calculateTaskPrice(entry $entry, VendorCatalogItem $vendorCatalogItem)
	{
		$taskPrice = call_user_func($vendorCatalogItem->getPricing()->getPriceFunction(), $entry, $vendorCatalogItem->getPricing()->getPricePerUnit());
		$taskPrice += $vendorCatalogItem->getFixedPriceAddons();
		return $taskPrice;
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
			return true;

		$entryTaskPrice = self::calculateTaskPrice($entry, $catalogItem);
		
		KalturaLog::debug("allowedCredit [$allowedCredit] creditUsed [$creditUsed] entryTaskPrice [$entryTaskPrice]");
		$remainingCredit = $allowedCredit - ($creditUsed  + $entryTaskPrice);
		
		return $remainingCredit >= 0 ? true : false;
	}

	/**
	 * @param $entry
	 * @param $catalogItem
	 * @param $reachProfile
	 * @return bool
	 */
	public static function hasCreditExpired(ReachProfile $reachProfile)
	{
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
			return true;

		$creditUsed = $reachProfile->getUsedCredit();
		$entryTaskPrice = $entryVendorTask->getPrice();
		
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
	
	public static function isDuplicateTask($entryId, $catalogItemId, $partnerId, $version)
	{
		$activeTask = EntryVendorTaskPeer::retrieveActiveTasks($entryId, $catalogItemId, $partnerId, $version);
		if($activeTask)
			return true;
		
		return false;
	}
	
	public static function isEntryTypeSupported($type)
	{
		switch ($type)
		{
			case entryType::MEDIA_CLIP:
				return true;
			
			default:
				return false;
		}
	}
}