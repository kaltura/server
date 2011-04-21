<?php
/**
 * Plugins may add bulk upload types
 * The bulk upload type should enable object loading of kBulkUploadJobData, KalturaBulkUploadJobData and KBulkUploadEngine
 * The plugin must expend BulkUploadType enum with the added new type
 * 
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaBulkUpload extends IKalturaBase, IKalturaEnumerator, IKalturaObjectLoader
{
	/**
	 * Returns the correct file extension for bulk upload type
	 * @param int $enumValue code API value
	 */
	public static function getFileExtension($enumValue);
}