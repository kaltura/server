<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchResponse extends KalturaObject
{
    /**
     * @var int
     * @readonly
     */
    public $totalCount;

    /**
     * @var KalturaESearchResultArray
     * @readonly
     */
    public $objects;
}
