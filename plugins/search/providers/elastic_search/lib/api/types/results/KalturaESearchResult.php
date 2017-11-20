<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchResult extends KalturaObject
{
    /**
     * @var KalturaObject
     */
    public $object;

	/**
	 * @var string
	 */
	public $highlight;

    /**
     * @var KalturaESearchItemDataResultArray
     */
    public $itemsData;

    private static $map_between_objects = array(
        'object',
		'highlight',
        'itemsData',
    );

    protected function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

	abstract function getAPIObject($srcObj);

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$object = $this->getAPIObject($srcObj);
		$object->fromObject($srcObj->getObject());
		$this->object = $object;
		return parent::doFromObject($srcObj, $responseProfile);
	}

}
