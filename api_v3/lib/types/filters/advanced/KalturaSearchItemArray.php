<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSearchItemArray extends KalturaTypedArray
{
	/**
	 * @param array $arr
	 * @return KalturaSearchItemArray
	 */
	public static function fromDbArray(array $arr = null, IResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaSearchItemArray();
		if(!$arr || !count($arr))
			return $newArr;
			
		foreach ( $arr as $obj )
		{
			$kalturaClass = $obj->getKalturaClass();
			if(!class_exists($kalturaClass))
			{
				KalturaLog::err("Class [$kalturaClass] not found");
				continue;
			}
				
			$nObj = new $kalturaClass();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
	/**
	 * @return array
	 */
	public function toObjectsArray()
	{
		KalturaLog::debug("To objects array: count [" . count($this) . "]");
		
		$ret = array();
		foreach($this as $item)
		{
			KalturaLog::debug('Item type [' . get_class($item) . ']');
			$ret[] = $item->toObject();
		}
			
		return $ret;
	}
	
	public function __construct( )
	{
		return parent::__construct ( "KalturaSearchItem" );
	}
}
?>