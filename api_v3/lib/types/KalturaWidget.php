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
	 * @var KalturaWidgetSecurityType
	 */
	public $securityType;
	
	/**
	 * @var int
	 */
	public $securityPolicy;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var time
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
	
	/**
	 * 
	 * Should enforce entitlement on feed entries
	 * @var bool
	 */
	public $enforceEntitlement;
	
	/**
	 * Set privacy context for search entries that assiged to private and public categories within a category privacy context.
	 *  
	 * @var string
	 * $filter eq
	 */
	public $privacyContext;

	/**
	 * 
	 * Addes the HTML5 script line to the widget's embed code
	 * @var bool
	 */
	
	public $addEmbedHtml5Support = false;
	
	private static $map_between_objects = array
	(
		"id" , "sourceWidgetId" , "rootWidgetId" , "partnerId" , "entryId" , "uiConfId" , "widgetHTML" , 
		"securityType" , "securityPolicy" , "createdAt" , "updatedAt" , "partnerData", "enforceEntitlement", "privacyContext", "addEmbedHtml5Support",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
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