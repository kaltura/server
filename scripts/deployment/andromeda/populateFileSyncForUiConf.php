<?php

require_once ( "../loaddata/define.php" );

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/myContentStorage.class.php');

ini_set("memory_limit","128M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

// fix uiConf
$c = new Criteria();

$count = uiConfPeer::doCount($c);
print "[$count] uiConfs\n";
$c->setLimit(100);

$limit = 4000;
$offset = 0;
$total = 0;

$no_files = array ();
$fixed_duration = array();

$content = myContentStorage::getFSContentRootPath();

$sub_types = array (uiconf::FILE_SYNC_UICONF_SUB_TYPE_DATA ,  uiconf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES  );

	
$list_of_bad_uiconf = array();

$singleUiConfPopulation = 0;
$singleUiConfPopulated = false;
if(isset($argv[1]))
{
	$singleUiConfPopulation = $argv[1];
}

while(1)
{
	if($singleUiConfPopulation > 0 && $singleUiConfPopulated)
	{
		echo 'Finished populating single uiconf '.$singleUiConfPopulation.' - exiting... ';
		break;
	}
	if($singleUiConfPopulation == 0)
	{
		$c->setOffset($offset);
		$uiConfs = uiConfPeer::doSelect($c);
		if (count($uiConfs) == 0)	break;
	}
	else
	{
		$singleUiConf = uiConfPeer::retrieveByPK($singleUiConfPopulation);
		if(!$singleUiConf)
			die("requested population of uiconf $singleUiConfPopulation - but it does not exist");
		
		$uiConfs[] = $singleUiConf;
		$singleUiConfPopulated = true;		
	}

	foreach($uiConfs as $uiConf)
	{
		if ( $limit < $total )
		{
			TRACE ( "Exiting - reached the desired limit of [$limit]");
			break(2);
		}
		
		TRACE ("($offset) ($total) Setting path for [" . $uiConf->getId() . "]" );
		$total++;
		
		$version = null;
		
		foreach ( $sub_types as $sub_type )
		{
			try
			{
				$sync_key = $uiConf->getSyncKey ( $sub_type , $version );
				if ( kFileSyncUtils::file_exists( $sync_key ))
				{
					TRACE ("($offset) ($total) [" . $uiConf->getId() . "][$sub_type] already exists on [" . $sync_key->getFullPath() . "]" );
				}
				else
				{
					list ( $root_path , $file_path ) = $uiConf->generateFilePathArr( $sub_type , $version ) ;
					
					$full_path = $root_path . $file_path;

					if ( file_exists ( $full_path ) )
					{
						kFileSyncUtils::createSyncFileForKey( $sync_key );
						TRACE ("($offset) ($total) [" . $uiConf->getId() . "][$sub_type] created [$full_path] OK" );
					}
					else
					{
						if ( $sub_type == uiconf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES )
							TRACE ("warning: ($offset) ($total) [" . $uiConf->getId() . "][$sub_type] cannot find file on [$full_path]" );
						else
							TRACE ("error: ($offset) ($total) [" . $uiConf->getId() . "][$sub_type] cannot find file on [$full_path]" );
					}
				}
			}
			catch ( Exception $ex )
			{
				TRACE ( $ex->getMessage() );
				$list_of_bad_uiconf[] = $uiConf->getId();
			}
		}				
	}
		
	$offset += 100;
}
	
TRACE ( "Bad ones:");
TRACE ( var_dump ( $list_of_bad_uiconf ) ,true );
?>
