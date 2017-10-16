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
    public $absolute_duration;

    /**
     * @var KalturaKeyValueArray
     */
    public $licenseParams;
}