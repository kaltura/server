<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaWidget extends KalturaObject implements IFilterable 
{
	/**
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var string
	 * @filter eq
	 */
	public $sourceWidgetId;
	
	/**
	 * @var string
	 * @readonly
	 * @filter eq
	 */
	public $rootWidgetId;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq
	 */
	public $partnerId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @filter eq
	 */
	public $uiConfId;
	
	/**
	 * @var string
	 */
	private $customData;
	
	/**
	 * @var KalturaWidgetSecurityType
	 */
	public $securityType;
	
	/**
	 * @var int
	 */
	public $securityPolicy;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte
	 */
	public $updatedAt;
	
	/**
	 * Can be used to store various partner related data as a string 
	 * @var string
	 * @filter like
	 */
	public $partnerData;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $widgetHTML;

	private static $map_between_objects = array
	(
		"id" , "sourceWidgetId" , "rootWidgetId" , "partnerId" , "entryId" , "uiConfId" , "widgetHTML" , 
		"securityType" , "securityPolicy" , "createdAt" , "updatedAt" , "partnerData"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromWidget ( widget $entry )
	{
		parent::fromObject( $entry );
	}
	
	public function toWidget () 
	{
		$user = new widget();
		$skip_props = array ( "widgetHTML" );
		return parent::toObject( $user , $skip_props );
	}
	
	public function getExtraFilters()
	{ 
		return array();		
	}
	
	public function getFilterDocs()
	{
		return array();	
	}
}
?>