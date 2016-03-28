<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryServerNodeArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
	{
		$newArr = new KalturaEntryServerNodeArray();
		foreach($arr as $obj)
		{
			/* @var $obj KalturaEntryServerNode */
			$nObj = KalturaEntryServerNode::getInstance($obj);
			if (!$nObj)
			{
				throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_OBJECT_TYPE_ERROR, $obj->getServerType(), $obj->getId());
			}
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

	public function __construct()
	{
		return parent::__construct("KalturaEntryServerNode");
	}

}