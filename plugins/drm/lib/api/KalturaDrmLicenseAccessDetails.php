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
    public $policyName;
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

}