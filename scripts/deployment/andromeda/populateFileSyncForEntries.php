<?php

require_once ( "../loaddata/define.php" );

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/myContentStorage.class.php');

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$ids = @$argv[1];
// 
$c = new Criteria();
if ( $ids )
{
	$id_arr = explode ( "," , $ids );
	$c->add ( entryPeer::ID , $id_arr , Criteria::IN );
}

$count = entryPeer::doCount($c);
print "[$count] entry\n";
$c->setLimit(100);

$limit = 4000;
$offset = 0;
$total = 0;

$no_files = array ();

$list_of_bad_entries = array();

$sub_types = array ( entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA , entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT , entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB  
	, entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE ,	entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD );

$singleEntryPopulation = '';
$singleEntryPopulated = false;
if(isset($argv[1]))
{
	$singleEntryPopulation = $argv[1];
}
	
while(1)
{
	if($singleEntryPopulation != '' && $singleEntryPopulated)
	{
		echo 'Finished populating single entry '.$singleEntryPopulation.' - exiting... ';
		break;
	}
	if($singleEntryPopulation == '')
	{	
		$c->setOffset($offset);
		$entries = entryPeer::doSelect($c);
		if (count($entries) == 0)	break;
	}
	else
	{
		$singleEntry = entryPeer::retrieveByPK($singleEntryPopulation);
		if(!$singleEntry)
			die("requested population of entry $singleEntryPopulation - but it does not exist");
		
		$entries[] = $singleEntry;
		$singleEntryPopulated = true;		
	}	

	foreach($entries as $entry)
	{
		if ( $limit < $total )
		{
			TRACE ( "Exiting - reached the desired limit of [$limit]");
			break(2);
		}
		
		TRACE ("($offset) ($total) Setting paths for [" . $entry->getId() . "]" );
		$total++;
		
		foreach ( $sub_types as $sub_type )
		{
			// TODO -iterate all versions + iterate all downloads for sub type FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD
			if ( $sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD )
			{
				$versions = array();
				list ( $root_path , $file_path ) = $entry->generateFilePathArr( $sub_type , null ) ;
				$download_pattern = $root_path . $file_path . "*";
				
				foreach ( glob($download_pattern) as $file)
				{
					$versions[] = pathinfo( $file , PATHINFO_EXTENSION );
				} 
			}
			else
			{
				if ( $entry->getType() == entry::ENTRY_TYPE_SHOW && $sub_type == entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA )
				{
					$versions_struct = $entry->getAllVersions();
					if (  $versions_struct )
					{
						foreach ( $versions_struct as $obj )
						{
							$versions[] = myContentStorage::getVersion( $obj[0] );
						}
					}
					else
					{
						$versions  = array ( null );
					}
				}
				else
					$versions  = array ( null );
			}

			foreach ( $versions as $version )
			{
				
				try
				{
					$sync_key = $entry->getSyncKey ( $sub_type , $version );
					if ( kFileSyncUtils::file_exists( $sync_key ))
					{
						TRACE ("($offset) ($total) [" . $entry->getId() . "][$sub_type] already exists on [" . $sync_key->getFullPath() . "]" );
					}
					else
					{
						list ( $root_path , $file_path ) = $entry->generateFilePathArr( $sub_type , $version ) ;
						$file_path = str_replace ( myContentStorage::getFSContentRootPath( ) , "" , $file_path );
						$full_path = $root_path . $file_path;
						if ( file_exists ( $full_path ) )
						{
							kFileSyncUtils::createSyncFileForKey( $sync_key );
							TRACE ("($offset) ($total) [" . $entry->getId() . "][$sub_type] created OK" );
						}
						else
						{
							TRACE ("($offset) ($total) [" . $entry->getId() . "][$sub_type] cannot find file on [$full_path]" );
						}
					}
				}
				catch ( Exception $ex )
				{
					TRACE ( $ex->getMessage() );
					$list_of_bad_entries[] = $entry->getId();
				}
			}
		}
	}
	$offset += 100;
}
	
TRACE ( "Bad ones:");
TRACE ( var_dump ( $list_of_bad_entries ) ,true );
?>