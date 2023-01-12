<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaPartnerFilter extends KalturaPartnerBaseFilter
{
    static private $map_between_objects = array
    (
        "partnerParentIdEqual" => "_eq_partner_parent_id",
        "partnerParentIdIn" => "_in_partner_parent_id",
        "adminEmailEqual" => "_eq_admin_email"
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

    /**
     * @var string
     * @requiresPermission all
     */
    public $adminEmailEqual;

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new partnerFilter();
	}
}
