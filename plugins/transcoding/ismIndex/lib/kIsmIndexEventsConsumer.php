<?php
class kIsmIndexEventsConsumer implements kObjectChangedEventConsumer
{	
		/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(
			$object instanceof flavorAsset
			&&	in_array(assetPeer::STATUS, $modifiedColumns)
			&&  ($object->getStatus() == flavorAsset::ASSET_STATUS_READY || $object->getStatus() == flavorAsset::ASSET_STATUS_DELETED)
			&&  $object->hasTag(assetParams::TAG_ISM)
			&&  !$object->getentry()->getStatus() == entryStatus::DELETED
			&& 	!$object->getentry()->getReplacingEntryId()
		)
			return true;
			
		return false;
	}

		/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{	
		$flavorParams = assetParamsPeer::retrieveByPKNoFilter($object->getFlavorParamsId());
		if($flavorParams && $flavorParams->getConversionEngines() != conversionEngineType::EXPRESSION_ENCODER3)	
			kSmoothManifestHelper::mergeManifestFiles($object->getEntryId(), assetParams::TAG_ISM, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC);
							
		return true;
	}
}