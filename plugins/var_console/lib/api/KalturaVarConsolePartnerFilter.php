<?php
/**
 * @package plugins.var_console
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
    
    private $map_between_objects = array
    (
    	"groupTypeEq" => "_eq_group_type",
        "groupTypeIn" => "_in_group_type",
    );
}