<?php
/**
 * Enable the plugin to handle bulk upload additional data
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaImportHandler extends IKalturaBase
{
	/**
	 * @param array $curlInfo
	 * @param $data
	 * @param KalturaImportJobData $importData
	 */
	public static function handleImportContent(array $curlInfo, $data, $importData);	
}