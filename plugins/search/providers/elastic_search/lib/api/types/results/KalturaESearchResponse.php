<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchResponse extends KalturaObject
{
    /**
     * @var int
     * @readonly
     */
    public $totalCount;

}
