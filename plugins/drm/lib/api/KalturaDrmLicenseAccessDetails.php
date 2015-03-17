<?php
/**
 * @package plugins.drm
 * @subpackage api.objects
 */
class KalturaDrmLicenseAccessDetails extends  KalturaObject {

    /**
     * Drm policy name
     *
     * @var string
     */
    public $policy;
    /**
     * movie duration in seconds
     *
     * @var int
     */
    public $duration;
    /**
     * playback window in seconds
     *
     * @var int
     */
    public $absoluteExpiration;

    /*
     * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
     */
    private static $map_between_objects = array(
        'policy',
        'duration',
        'absolute_expiration',
    );

    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }
}