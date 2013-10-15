<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaContextTypeHolderArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaContextTypeHolderArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $type)
		{
			$nObj = self::getInstanceByType($type);				
			$nObj->type = $type;
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByType($type)
	{
		switch($type)
		{
			case ContextType::DOWNLOAD:
			case ContextType::PLAY:
			case ContextType::THUMBNAIL:
			case ContextType::METADATA:
				return new KalturaAccessControlContextTypeHolder();
			default:
				return new KalturaContextTypeHolder();
		}		
	}
	
	public function __construct()
	{
		parent::__construct("KalturaContextTypeHolder");	
	}
}