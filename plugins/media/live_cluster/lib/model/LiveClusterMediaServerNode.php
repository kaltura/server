<?php


class LiveClusterMediaServerNode extends MediaServerNode
{

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        parent::applyDefaultValues();

        $this->setType(LiveClusterPlugin::getLiveClusterMediaServerTypeCoreValue(LiveClusterMediaServerNodeType::LIVE_CLUSTER_MEDIA_SERVER));
    }

    /**
     * @param string $serviceName
     * @return KalturaMediaServerClient
     */
    public function getWebService($serviceName)
    {
        return null;
    }

    public function getLiveWebServiceName()
    {
        return 'NO_IMPLEMENTED';
    }

    public function getPlaybackHost($protocol = 'http', $format = null, $baseUrl = null, $deliveryType = null)
    {
        $hostname = $this->getHostname();
        if(!$this->getIsExternalMediaServer())
            $hostname = preg_replace('/\..*$/', '', $hostname);

        $mediaServerConfig = kConf::getMap('media_servers');
        if($baseUrl && $baseUrl !== '')
        {
            $domain = preg_replace("(https?://)", "", $baseUrl);
            $domain = rtrim($domain, "/");
        }
        else
        {
            $domain = $this->getDomainByProtocolAndFormat($mediaServerConfig, $protocol, $format);
            $port = $this->getPortByProtocolAndFormat($mediaServerConfig, $protocol, $format);
            $domain = "$domain:$port";
        }

        $playbackHost = "$protocol://$domain/";
        $playbackHost = str_replace("{hostName}", $hostname, $playbackHost);
        return $playbackHost;
    }

    public function getEnvDc()
    {

    }
}