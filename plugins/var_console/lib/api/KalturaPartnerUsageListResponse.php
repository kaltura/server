<?php
/**
 * @package plugins.varConsole
 * @subpackage api.types
 */
class KalturaPartnerUsageListResponse extends KalturaListResponse
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