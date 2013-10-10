<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUiConf extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Name of the uiConf, this is not a primary key
	 * @var string
	 * @filter like
	 */
	public $name;
	
	/**
	 * 
	 * @var string
	 */
	public $description;
	
	/**
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;	
	

	/**
	 * @var KalturaUiConfObjType $objType
	 * @filter eq,in
	 */
	public $objType;

	/**
	 * @var string
	 * @readonly
	 */
	public $objTypeAsString;
	
	/**
	 *
	 * @var int
	 */
	public $width;
	
	/**
	 * @var int
	 */
	public $height;
	
	/**
	 * @var string
	 */
	public $htmlParams;
	
	/**
	 * @var string
	 */
	public $swfUrl;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $confFilePath;
	
	/**
	 * @var string
	 */
	public $confFile;

	/**
	 * @var string
	 * @hidden
	 */
	public $confFileFeatures;
	
	/**
	 * @var string
	 */
	public $config;
	
	/**
	 * @var string
	 */
	public $confVars;
	
	
	/**
	 * @var bool
	 */
	public $useCdn;
	
	/**
	 * @var string
	 * @filter mlikeor, mlikeand
	 */
	public $tags;
	
	
	/**
	 * @var string
	 */
	public $swfUrlVersion;
	
	
	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	
	/**
	 * Entry creation date as Unix timestamp (In seconds)
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
		
	/**
	 *
	 * @var KalturaUiConfCreationMode $creationMode
	 * @filter eq,in
	 */
	public $creationMode;
	
	/**
	 * @var string
	 */
	public $html5Url;
		
	private static $map_between_objects = array
	(
		"id" ,
		"creationMode" , 
		"partnerId" ,
	 	"objType" , 
	 	"objTypeAsString" , 
	 	"name" , 
	 	"description" , 
	 	"width" , 
	 	"height" ,
		"htmlParams", 
		"swfUrl" , 
		//"confFilePath" , 
		"confFile" , 
		"confFileFeatures" , 
		"confVars" , 
		"useCdn" , 
		"tags" , 
		"swfUrlVersion" , 
		"createdAt" , 
		"updatedAt", 
		"html5Url",
		"config",
        
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function fromUiConf ( uiConf  $uiConf )
	{
		parent::fromObject( $uiConf );
	}
	
	public function toUiConf () 
	{
		$uiConf = new uiConf();
		return parent::toObject( $uiConf )	;
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