<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/18/2017
 * Time: 11:53 PM
 */

/**
 * @package plugins.beacon
 * @subpackage api.filters
 */
class KalturaBeaconFilter {

    /**
     * @var KalturaBeaconObjectTypes
     */
    public $relatedObjectType;

    /**
     * @var string
     */
    public $eventType;

    /**
     * @var string
     */
    public $objectId;

    /**
     * @var string
     */
    public $privateData;

    /**
     * @var string
     */
    public $created_at;
}