<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryMetadataArray extends KalturaTypedArray
{
	public static function fromEntryAndMetadataArray($entries, $metadatas, $isAdmin = false)
	{
		$newArr = new KalturaEntryMetadataArray();
		if ($entries == null)
			return $newArr;

		foreach ($entries as $obj)
		{
    		$entry = KalturaEntryFactory::getInstanceByType($obj->getType(), $isAdmin);
			$entry->fromObject($obj);
			
    		$entryMetadata = new KalturaEntryMetadata();
    		$entryMetadata->entry = $entry;
    		$entryMetadata->metadatas = new KalturaMetadataArray();
    		
			$newArr[$entry->id] = $entryMetadata;
		}
	
		foreach ($metadatas as $obj)
		{
    		$metadata = new KalturaMetadata();
			$metadata->fromObject($obj);
			
			if($metadata->metadataObjectType != KalturaMetadataObjectType::ENTRY)
				continue;
				
			if(!isset($newArr[$metadata->objectId]))
				continue;
				
			$newArr[$metadata->objectId]->metadatas[] = $metadata;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaBaseEntry");	
	}
}