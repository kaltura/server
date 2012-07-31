<?php
/**
 * @package plugins.varConsole
 * @subpackage model.filters
 *
 */
class KalturaVarConsolePartnerFilter extends KalturaPartnerFilter
{
    /**
     * @var KalturaPartnerGroupType
     */
    public $groupTypeEq;
    
    /**
     * @var string
     */
    public $groupTypeIn;
    
    /**
     * @var string
     */
    public $partnerPermissionsExist;
    
    private $map_between_objects = array
    (
    	"groupTypeEq" => "_eq_partner_group_type",
        "groupTypeIn" => "_in_partner_group_type",
        "partnerPermissionsExist" => "_partner_permissions_exist",
    );
    
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}