<?php


class OpenCalaisReachVendorPlugin extends KalturaPlugin implements IKalturaPending
{
    const PLUGIN_NAME = 'OpenCalaisReachVendor';
    /**
     * @inheritDoc
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function dependsOn()
    {
        $reachPluginDependency = new KalturaDependency(ReachPlugin::getPluginName());
        $reachInternalPluginDependency = new KalturaDependency(ReachInternalPlugin::PLUGIN_NAME);
        $transcriptPluginDependency = new KalturaDependency(TranscriptPlugin::PLUGIN_NAME);
        $metadataPluginDependency = new KalturaDependency(MetadataPlugin::PLUGIN_NAME);

        return array($reachPluginDependency, $reachInternalPluginDependency, $transcriptPluginDependency, $metadataPluginDependency);
    }
}