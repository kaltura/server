<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaPlaybackContextDataContributor
{
    /**
     * Receives the context-data result and adds an instance of KalturaPluginData to the pluginData containing
     * the specific plugins context-data.
     *
     * @param entry $entry
     * @param kPlaybackContextDataParams $entryPlayingDataParams
     * @param kPlaybackContextDataResult $result
     * @param kContextDataHelper $contextDataHelper
     */
    public function contributeToPlaybackContextDataResult(entry $entry, kPlaybackContextDataParams $entryPlayingDataParams, kPlaybackContextDataResult $result, kContextDataHelper $contextDataHelper);

    /**
     * @param $streamerType
     * @return boolean
     */
    public function isSupportStreamerTypes($streamerType);

    /**
     * @param $drmProfile
     * @param $scheme
     * @param $customDataObject
     * @return boolean
     */
    public function constructUrl($drmProfile, $scheme, $customDataObject);

}
