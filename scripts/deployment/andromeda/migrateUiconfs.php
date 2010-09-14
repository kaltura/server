<?php
class migrateUiconfs extends AndromedaMigration
{
	/**
	 * @param uiConf $uiConf
	 * @return bool
	 */
	public static function migrateSingleUiconf(uiConf $uiConf)
	{
		$content = myContentStorage::getFSContentRootPath();
		$sub_types = array (
			uiconf::FILE_SYNC_UICONF_SUB_TYPE_DATA ,
			uiconf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES
		);
		$version = null;
		$return_flag = 0;
		
		$conf_path = $uiConf->internalGetParentConfFilePath();
		foreach ( $sub_types as $sub_type )
		{
			try
			{
				if($sub_type == uiconf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES)
				{
					$conf_path = str_replace ( 'xml' , "features.xml" , $conf_path );						
				}
				$conf_path = str_replace($content, '', $conf_path); // remove '/web/' if exists
				$sync_key = $uiConf->getSyncKey ( $sub_type , $version );
				if ( kFileSyncUtils::file_exists( $sync_key ))
				{
					self::logPartner("     Single uiConf migration [{$uiConf->getId()}]: already have fileSync for sub type: [$sub_type]- OK.");
					if($return_flag === 0 && $sub_type == uiconf::FILE_SYNC_UICONF_SUB_TYPE_DATA)
						$return_flag++;
				}
				else
				{
					$full_path = $content . $conf_path;
					
					if ( file_exists ( $full_path ) )
					{
						$sync_key->file_root = $content;
						$sync_key->file_path = $conf_path;
						kFileSyncUtils::createSyncFileForKey( $sync_key );
						self::logPartner("     Single uiConf migration [{$uiConf->getId()}]: created new fileSync for sub type: [$sub_type]- OK.");
						if($return_flag === 0 && $sub_type == uiconf::FILE_SYNC_UICONF_SUB_TYPE_DATA)
							$return_flag++;
					}
					else
					{
						if ( $sub_type == uiconf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES )
						{
							self::logPartner("     Single uiConf migration [{$uiConf->getId()}]: has no 'features' file, not mandatory, considered OK", Zend_Log::WARN);
						}
						else
						{
							self::logPartner("     ERROR!     Single uiConf migration [{$uiConf->getId()}]: has no conf file, Requires fix !", Zend_Log::ERR);
							self::logPartner("          looked for file {[$full_path]}");
							$return_flag = 0;
						}
					}
				}
			}
			catch ( Exception $ex )
			{
				self::logPartner("     ERROR!     Single uiConf migration [{$uiConf->getId()}]: tried to create fileSync but failed. error: ".$ex->getMessage(), Zend_Log::CRIT);
				if($sub_type == uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA)
					$return_flag = 0;
			}			
		}
		return (bool)$return_flag;
	}
	
	private static $failedIds;
	
	public static function getFailedIds()
	{
		return self::$failedIds;
	}
	
	/**
	 * migrate a list of uiconf objects return value is integer: 0 - all failed, 1 - all OK, 2 - some failed
	 * @param array $arrUiconfs
	 * @return int
	 */
	public static function migrateUiconfList($arrUiconfs)
	{
		self::$failedIds = array();
		if(!count($arrUiconfs) || !is_array($arrUiconfs))
			return FALSE;
		
		foreach($arrUiconfs as $uiconf)
		{
			$result = self::migrateSingleUiconf($uiconf);
			if(!$result) self::$failedIds[] = $uiconf->getId();
		}
		if(count(self::$failedIds) == 0)
			return 1;
		elseif(count(self::$failedIds) == count($arrUiconfs))
			return 0;
		else
			return 2;
	}
}