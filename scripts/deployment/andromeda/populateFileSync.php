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

$list_of_bad_uiconf = array();

while(1)
{
	$c->setOffset($offset);
	$uiConfs = uiConfPeer::doSelect($c);
	if (count($uiConfs) == 0)	break;

	foreach($uiConfs as $uiConf)
	{
		if ( $limit < $total )
		{
			TRACE ( "Exiting - reached the desired limit of [$limit]");
			break(2);
		}
		try
		{
			TRACE ("($offset) ($total) Setting path for [" . $uiConf->getId() . "]" );
			$total++;
			
			$uiConf->getConfFile();
			$uiConf->getConfFileFeatures();
			
			TRACE ("($offset) ($total) [" . $uiConf->getId() . "] OK" );
			
		}
		catch ( Exception $ex )
		{
			TRACE ( $ex->getMessage() );
			$list_of_bad_uiconf[] = $uiConf->getId();
		}
	}
	$offset += 100;
}
	
TRACE ( "Bad ones:");
TRACE ( var_dump ( $list_of_bad_uiconf ) ,true );
?>