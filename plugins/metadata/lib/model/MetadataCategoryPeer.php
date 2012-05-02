<?php
/**
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataCategoryPeer extends categoryPeer implements IMetadataPeer
{
	public function setPartnerId($partnerId)
	{
		self::addPartnerToCriteria($partnerId);
	}
}