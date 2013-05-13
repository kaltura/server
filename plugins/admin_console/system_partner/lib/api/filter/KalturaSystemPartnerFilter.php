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
	);
	
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
}