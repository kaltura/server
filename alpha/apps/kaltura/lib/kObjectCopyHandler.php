<?php

class kObjectCopyHandler implements kObjectCopiedEventConsumer
{
	protected static $idsMap = array();

	public static function mapIds($className, $fromId, $toId)
	{
		if(!isset(self::$idsMap[$className]))
			self::$idsMap[$className] = array();
			
		self::$idsMap[$className][$fromId] = $toId;
	}
	
	public static function getMappedId($className, $fromId)
	{
		if(!isset(self::$idsMap[$className]) || !isset(self::$idsMap[$className][$fromId]))
			return null;
			
		return self::$idsMap[$className][$fromId];
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof assetParams)
			return true;
			
		if($fromObject instanceof assetParamsOutput)
			return true;
		
		if($fromObject instanceof conversionProfile2)
			return true;
			
		if($fromObject instanceof entry)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof assetParams)
			self::mapIds('assetParams', $fromObject->getId(), $toObject->getId());
		
		if($fromObject instanceof conversionProfile2)
			self::mapIds('conversionProfile2', $fromObject->getId(), $toObject->getId());
		
		if($fromObject instanceof assetParamsOutput)
		{
			$flavorParamsId = self::getMappedId('assetParams', $fromObject->getFlavorParamsId());
			if($flavorParamsId)
			{
				$toObject->setFlavorParamsId($flavorParamsId);
				$toObject->save();
			}
		}
		
		if($fromObject instanceof entry)
		{
			$conversionProfileId = self::getMappedId('conversionProfile2', $fromObject->getConversionProfileId());
			if($conversionProfileId)
			{
				$toObject->setConversionProfileId($conversionProfileId);
				$toObject->save();
			}
		}
		
		return true;
	}
}