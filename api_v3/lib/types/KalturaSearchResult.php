<?php
/**
 * @package api
 * @subpackage objects
 * @deprecated
 */
class KalturaSearchResult extends KalturaSearch
{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $title;
	
	/**
	 * @var string
	 */
	public $thumbUrl;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var string
	 */
	public $tags;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 */
	public $sourceLink;
	
	/**
	 * @var string
	 */
	public $credit;
	
	/**
	 * @var KalturaLicenseType
	 */
	public $licenseType;
	
	/**
	 * @var string
	 */
	public $flashPlaybackType;
	
	/**
	 * @var string
	 */
	public $fileExt;
	
	private static $map_between_objects = array
	(
		"id" , 
		"title" , 
		"thumbUrl" => "thumb" , 
		"description" , "tags" , 
		"url" , 
		"sourceLink" => "source_link" , 
		"credit" , 
		"licenseType" => "license" ,
	    "flashPlaybackType" => "flash_playback_type",
	    "fileExt" => "file_ext"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromSearchResult( $search_result , KalturaSearch $search )
	{
		parent::fromArray( $search_result );
		$this->mediaType = $search->mediaType;
		$this->searchSource = $search->searchSource;
		$this->keyWords = $search->keyWords;
	}
	
	public function toSearchResult( )
	{
		
	}
}