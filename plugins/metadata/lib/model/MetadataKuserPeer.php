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
	private $partnerId;
	
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;		
	}
	
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		return parent::getKuserByPartnerAndUid($this->partnerId, $pk, true);
	}
}