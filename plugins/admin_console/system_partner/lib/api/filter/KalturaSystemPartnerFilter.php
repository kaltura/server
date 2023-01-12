<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.filters
 */
class KalturaSystemPartnerFilter extends KalturaPartnerFilter
{
	static private $map_between_objects = array
	(
		"partnerParentIdEqual" => "_eq_partner_parent_id",
		"partnerParentIdIn" => "_in_partner_parent_id",
		"adminEmailEqual" => "_eq_admin_email"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
 * @see KalturaFilter::getCoreFilter()
 */
	protected function getCoreFilter()
	{
		return new systemPartnerFilter();
	}
	
	/**
	 * @var int
	 * @requiresPermission write
	 */
	public $partnerParentIdEqual;

	/**
	 * @var string
	 * @requiresPermission all
	 */
	public $partnerParentIdIn;

	/**
	 * @var string
	 * @requiresPermission all
	 */
	public $adminEmailEqual;

}