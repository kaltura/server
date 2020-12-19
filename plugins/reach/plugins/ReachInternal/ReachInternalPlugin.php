<?php

/**
 * Enable Reach internal processing queue feature.
 * @package plugins.reachInternal
 */
class ReachInternalPlugin extends KalturaPlugin implements  IKalturaPending
{
    const PLUGIN_NAME = 'ReachInternal';

    /**
     * @inheritDoc
     */
    public static function dependsOn()
    {
        $reachPluginDependency = new KalturaDependency(ReachPlugin::getPluginName());

        return array($reachPluginDependency);
    }

    /**
     * @inheritDoc
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

}