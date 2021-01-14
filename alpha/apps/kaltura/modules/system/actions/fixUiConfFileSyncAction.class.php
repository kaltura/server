<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( __DIR__ . "/kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class fixUiConfFileSyncAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;//myDbHelper::DB_HELPER_CONN_PROPEL2
		
		
		$uiConfId = $this->getRequestParameter("id");
		
		$uiConf = uiConfPeer::retrieveByPK($uiConfId);
		
		$subTypes = array (uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA, uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES);
		
		foreach($subTypes as $subType)
		{
			if ($subType == uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA)
				echo ("Data:".PHP_EOL);
			else if ($subType == uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES)
				echo ("Features:".PHP_EOL);
				
			$syncKey = $uiConf->getSyncKey($subType);
			if (kFileSyncUtils::file_exists($syncKey))
			{
				echo("File sync already exists.".PHP_EOL);
			}
			else
			{
				list($rootPath, $filePath) = kPathManager::getFilePath($syncKey, kPathManager::getStorageProfileIdForKey($syncKey));

				$fullPath = $rootPath . $filePath;

				if (file_exists($fullPath))
				{
					kFileSyncUtils::createSyncFileForKey($rootPath, $filePath, $syncKey);
					echo("Created successfully.".PHP_EOL);
				}
				else
				{
					echo("File not found:".PHP_EOL);
					echo($fullPath.PHP_EOL);
					echo("Not creating file sync.".PHP_EOL);
				}
			}
			echo PHP_EOL;
		}
		
		die;
	}
}

?>