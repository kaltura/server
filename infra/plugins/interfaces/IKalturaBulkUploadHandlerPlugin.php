<?php
interface IKalturaBulkUploadHandlerPlugin extends IKalturaPlugin
{
	/**
	 * @param string $entryId the new created entry
	 * @param array $data key => value pairs
	 */
	public static function handleBulkUploadData($entryId, array $data);	
}