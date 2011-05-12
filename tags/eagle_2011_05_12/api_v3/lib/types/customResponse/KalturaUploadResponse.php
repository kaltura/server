<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUploadResponse extends KalturaObject
{
	/**
	 * @var string
	 */
	public $uploadTokenId;

	/**
	 * @var int
	 */
	public $fileSize;
	
	/**
	 * 
	 * @var KalturaUploadErrorCode
	 */
	public $errorCode;
	
	/**
	 * 
	 * @var string
	 */
	public $errorDescription;
	
}