<?php
/**
 * @package plugins.varConsole
 * @subpackage api.types
 */
class KalturaPartnerUsageListResponse extends KalturaObject
{
    /**
     * @var KalturaVarPartnerUsageItem
     */
    public $total;
    /**
     * @var KalturaVarPartnerUsageArray
     */
    public $objects;
    
    /**
     * @var int
     */
    public $totalCount;
}