<?php
/**
 * Enable the plugin to handle bulk upload additional data
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaImportHandler extends IKalturaBase
{
	/**
	 * This method makes an intermediate change to the imported file or its related data under certain conditions.
	 * @param KCurlHeaderResponse $curlInfo
	 * @param KalturaImportJobData $importData
	 * @param Object $params
	 */
	public static function handleImportContent($curlInfo, $importData, $params);	
}