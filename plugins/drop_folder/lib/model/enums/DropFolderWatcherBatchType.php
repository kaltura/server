<?php

/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
class DropFolderWatcherBatchType implements IKalturaPluginEnum, BatchJobType
{
	const DROP_FOLDER_WATCHER = 'DropFolderWatcher';
	
	public static function getAdditionalValues()
	{
		return array(
			'DROP_FOLDER_WATCHER' => self::DROP_FOLDER_WATCHER
		);
	}
}
