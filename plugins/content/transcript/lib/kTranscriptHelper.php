<?php
/**
 * @package plugins.transcript
 * @subpackage lib
 */
class kTranscriptHelper
{
	const TOKEN_TYPE_WORD = 'word';
	
	const TOKEN_TYPE_PUNC = 'punc';
	
	public static function getAssetsByLanguage($entryId, array $assetTypes, $spokenLanguage, $additionalStatuses = array ())
	{
		$statuses = array(asset::ASSET_STATUS_QUEUED, asset::ASSET_STATUS_READY);
		$statuses = array_merge($statuses, $additionalStatuses);
		
		$resultArray = assetPeer::retrieveByEntryId($entryId, $assetTypes, $statuses);
	
		$objects = array ();
		foreach($resultArray as $object)
		{
			if($object->getLanguage() == $spokenLanguage)
			{
				$objects[$object->getContainerFormat()] = $object;
			}
		}	
		
		return $objects;
	} 
	
	/**
	 * construct new TranscriptAsset
	 * @param string $entryId
	 * @param int $partnerId
	 * @param string $language
	 * @param int $containerFormat
	 * @param int $providerType
	 * 
	 * @return TranscriptAsset
	 */
	public static function createTranscript ($entryId, $partnerId, $language, $containerFormat, $providerType = null)
	{
		$transcript = new TranscriptAsset();
		$transcript->setType(TranscriptPlugin::getAssetTypeCoreValue(TranscriptAssetType::TRANSCRIPT));
		$transcript->setEntryId($entryId);
		$transcript->setPartnerId($partnerId);
		$transcript->setLanguage($language);
		$transcript->setContainerFormat($containerFormat);
		
		if (!is_null($providerType))
			$transcript->setProviderType($providerType);
		
		return $transcript;
	}
}
