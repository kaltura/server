<?php
/**
 * @package plugins.bulkUploadXml
 * @subpackage Scheduler.BulkUpload
 */
class KalturaBulkUploadXmlException extends KalturaException
{
	public function __construct($message, $code, $arguments = null)
	{
		parent::__construct($message, $code, $arguments);
	}
}