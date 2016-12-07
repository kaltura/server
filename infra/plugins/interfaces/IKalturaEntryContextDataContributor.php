<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEntryContextDataContributor {

    /**
     * Receives the context-data result and adds an instance of KalturaPluginData to the pluginData containing
     * the specific plugins context-data.
     *
     * @param entry $entry
     * @param accessControlScope $contextDataParams
     * @param contributeToEntryContextDataResult $result
     * @return PluginData
     */
    public function contributeToEntryContextDataResult(entry $entry, accessControlScope $contextDataParams, kContextDataHelper $contextDataHelper);
}