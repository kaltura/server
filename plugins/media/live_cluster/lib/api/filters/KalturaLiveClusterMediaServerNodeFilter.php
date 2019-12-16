<?php
/**
 * @package plugins.liveCluster
 * @subpackage api.filters
 */

class KalturaLiveClusterMediaServerNodeFilter extends KalturaLiveClusterMediaServerNodeBaseFilter
{
    public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
    {
        if(!$type)
            $type = LiveClusterPlugin::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER);

        return parent::getTypeListResponse($pager, $responseProfile, $type);
    }
}