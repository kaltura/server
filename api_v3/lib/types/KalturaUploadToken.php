<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUploadToken extends KalturaObject implements IFilterable 
{
	/**
	 * Upload token unique ID
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * Partner ID of the upload token
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * User id for the upload token
	 * @var string
	 * @readonly
	 * @filter eq
	 */
	public $userId;
	
	/**
	 * Status of the upload token
	 * @var KalturaUploadTokenStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * Name of the file for the upload token, can be empty when the upload token is created and will be updated internally after the file is uploaded
	 * @var string
	 * @insertonly
	 * @filter eq
	 */
	public $fileName;
	
	/**
	 * File size in bytes, can be empty when the upload token is created and will be updated internally after the file is uploaded
	 * @var float
	 * @insertonly
	 * @filter eq
	 */
	public $fileSize;
	
	/**
	 * Uploaded file size in bytes, can be used to identify how many bytes were uploaded before resuming
	 * @var float
	 * @readonly
	 */
	public $uploadedFileSize;
	
	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter order
	 */
	public $createdAt;

	
	/**
	 * Last update date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 */
	public $updatedAt;

	/**
	 * Upload url - to explicitly determine to which domain to adress the uploadToken->upload call
	 * @var string
	 * @readonly
	 */
	public $url;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId", 
		"userId" => "puserId", 
		"status", 
		"fileName",
		"fileSize",
		"uploadedFileSize",
		"createdAt",
		"updatedAt",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($uploadTokenDb, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($uploadTokenDb, $responseProfile);
		if (kConf::hasParam('upload_uri'))
			$this->url = infraRequestUtils::getProtocol() . "://" . kConf::get('upload_uri');
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
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