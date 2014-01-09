<?php
class kSmoothProtectEventsConsumer implements kObjectChangedEventConsumer
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
			&&  $object->hasTag(PlayReadyPlugin::PLAY_READY_TAG)
			&&  !$object->getentry()->getStatus() == entryStatus::DELETED	
			&& !$object->getentry()->getReplacingEntryId()
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
			kSmoothManifestHelper::mergeManifestFiles($object->getEntryId(), PlayReadyPlugin::PLAY_READY_TAG, 
						entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM_ENC, entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC_ENC);
							
		return true;
	}
}