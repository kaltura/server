<?php

/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
class DropFolderBatchType implements IKalturaPluginEnum, BatchJobType
{
	const DROP_FOLDER_WATCHER = 'DropFolderWatcher';
	const DROP_FOLDER_CONTENT_PROCESSOR = 'DropFolderContentProcessor';
	
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_WATCHER' => self::DROP_FOLDER_WATCHER,
			'DROP_FOLDER_CONTENT_PROCESSOR' => self::DROP_FOLDER_CONTENT_PROCESSOR,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
