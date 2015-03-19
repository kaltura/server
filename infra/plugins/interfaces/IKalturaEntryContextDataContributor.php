<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEntryContextDataContributor {

    /**
     * Returns an object that is known only to the plugin, and extends the baseClass.
     *
     * @param string $entryId
     * @param KalturaEntryContextDataParams $contextDataParams
     * @param KalturaEntryContextDataResult $result
     * @return bool
     */
    public function contributeToEntryContextDataResult($entryId, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $result);
}