<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserParams extends KalturaESearchParams
{
	/**
	 * @var KalturaESearchUserOperator
	 */
	public $searchOperator;
	
	private static $mapBetweenObjects = array
	(
		"searchOperator",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchParams();
		}

		self::validateSearchOperator($this->searchOperator);
		
		$this->translateObjectId();

		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	protected function translateObjectId()
	{
		if (!$this->objectId)
		{
			return;
		}
		
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->objectId);
		if ($kuser)
		{
			$this->objectId = $kuser->getKuserId();
		}
	}
}
