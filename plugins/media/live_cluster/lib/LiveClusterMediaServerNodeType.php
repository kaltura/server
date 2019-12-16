<?php
/**
 * @package plugins.liveCluster
 * @subpackage lib.enum
 */
class LiveClusterMediaServerNodeType
{
    const LIVE_CLUSTER_MEDIA_SERVER = 'LIVE_CLUSTER_MEDIA_SERVER';

    public static function getAdditionalValues()
    {
        return array(
            'LIVE_CLUSTER_MEDIA_SERVER' => self::LIVE_CLUSTER_MEDIA_SERVER,
        );
    }

    /**
     * @return array
     */
    public static function getAdditionalDescriptions()
    {
        return array();
    }
}