<?php
/**
 * @package plugins.varConsole
 * @subpackage model.filters
 *
 */
class KalturaVarConsolePartnerFilter extends KalturaPartnerFilter
{
    /**
     * Eq filter for the partner's group type
     * @var KalturaPartnerGroupType
     */
    public $groupTypeEq;
    
    /**
     * In filter for the partner's group type
     * @var string
     */
    public $groupTypeIn;
    
    /**
     * Filter for partner permissions- filter contains comma-separated string of permission names which the returned partners should have.
     * @var string
     */
    public $partnerPermissionsExist;
    
    static private $map_between_objects = array
    (
    	"groupTypeEq" => "_eq_partner_group_type",
        "groupTypeIn" => "_in_partner_group_type",
        "partnerPermissionsExist" => "_partner_permissions_exist",
    );
    
    public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}