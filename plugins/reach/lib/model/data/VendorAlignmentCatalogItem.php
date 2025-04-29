<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_catalog_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *ac
 * @package plugins.reach
 * @subpackage model
 */
class VendorAlignmentCatalogItem extends VendorCatalogItem
{
	public function applyDefaultValues()
	{
		$this->setServiceFeature(VendorServiceFeature::ALIGNMENT);
	}
	
	public function getTaskVersion($entryId, $entryObjectType = KalturaEntryObjectType::ENTRY, $jobData = null)
	{
		$taskVersion = parent::getTaskVersion($entryId, $entryObjectType, $jobData);

		if(!$jobData || !($jobData instanceof kAlignmentVendorTaskData))
		{
			return $taskVersion;
		}

		/* @var $jobData kAlignmentVendorTaskData */
		$attachmentAsset = assetPeer::retrieveById($jobData->getTextTranscriptAssetId());
		if(!$attachmentAsset)
		{
			return $taskVersion;
		}

		return $taskVersion * $attachmentAsset->getVersion();
	}
	
} // VendorCaptionsCatalogItem
