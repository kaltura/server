<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEntryPlayingDataContributor
{
    /**
     * Receives the context-data result and adds an instance of KalturaPluginData to the pluginData containing
     * the specific plugins context-data.
     *
     * @param entry $entry
     * @param KalturaEntryPlayingDataParams $entryPlayingDataParams
     * @param KalturaEntryPlayingDataResult $result
     */
    public function contributeToEntryPlayingDataResult(entry $entry, KalturaEntryPlayingDataParams $entryPlayingDataParams, KalturaEntryPlayingDataResult $result);

    /**
     * @param $streamerType
     * @return boolean
     */
    public function isSupportStreamerTypes($streamerType);
}
