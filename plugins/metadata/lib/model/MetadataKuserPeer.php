<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataKuserPeer extends kuserPeer implements IMetadataPeer
{
	/**
	 * ID of the partner for filtered requests
	 * 
	 * @var int
	 */
	private static $partnerId;
	
	public function setPartnerId($partnerId)
	{
		self::$partnerId = $partnerId;		
	}
	
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		return parent::getKuserByPartnerAndUid(self::$partnerId, $pk, true);
	}
}