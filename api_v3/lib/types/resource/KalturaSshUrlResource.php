<?php
/**
 * Used to ingest media that is available on remote SSH server and accessible using the supplied URL, media file will be downloaded using import job in order to make the asset ready.
 *
 * @package api
 * @subpackage objects
 */
class KalturaSshUrlResource extends KalturaUrlResource
{
	
	/**
	 * SSH private key
	 * @var string
	 */
	public $privateKey;
	
	/**
	 * SSH public key
	 * @var string
	 */
	public $publicKey;
	
	/**
	 * Passphrase for SSH keys
	 * @var string
	 */
	public $keyPassphrase;
	
	private static $map_between_objects = array('privateKey', 'publicKey', 'keyPassphrase');
	
	/* (non-PHPdoc)
	 * @see KalturaUrlResource::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaContentResource::validateAsset()
	 */
	public function validateAsset(asset $dbAsset)
	{
		if( (!($dbAsset instanceof flavorAsset)) && (!($dbAsset instanceof CaptionAsset)) )
		{
			throw new KalturaAPIException(KalturaErrors::RESOURCE_TYPE_NOT_SUPPORTED, get_class($this));
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaUrlResource::toObject()
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(!$object_to_fill)
			$object_to_fill = new kSshUrlResource();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}