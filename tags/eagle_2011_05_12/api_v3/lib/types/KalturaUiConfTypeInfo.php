<?php
/**
 * Info about uiconf type
 * 
 * @see KalturaStringArray
 * @package api
 * @subpackage objects
 */
class KalturaUiConfTypeInfo extends KalturaObject
{
	/**
	 * UiConf Type
	 * 
	 * @var KalturaUiConfObjType
	 */
    public $type;
    
    /**
     * Available versions
     *  
     * @var KalturaStringArray
     */
    public $versions;
    
    /**
     * The direcotry this type is saved at
     * 
     * @var string
     */
    public $directory;
    
    /**
     * Filename for this UiConf type
     * 
     * @var string
     */
    public $filename;
    
	private static $mapBetweenObjects = array
	(
		"type",
		"versions",
		"directory",
		"filename",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
}