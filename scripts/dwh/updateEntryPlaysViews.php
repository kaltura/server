<?php
define ('ROOT_DIR','/monsoon/opt/kaltura/app');  
require_once(ROOT_DIR.'/api_v3/bootstrap.php');
require_once(ROOT_DIR . '/alpha/config/kConfLocal.php');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(ROOT_DIR, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(ROOT_DIR, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(ROOT_DIR, "infra", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/classMap.cache');
KAutoloader::register();
 
require_once(ROOT_DIR.DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");

require_once (ROOT_DIR.DIRECTORY_SEPARATOR.'alpha'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(ROOT_DIR. DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."KAutoloader.php");

$f = fopen("php://stdin", "r");
$count = 0;
$sphinxMgr = new kSphinxSearchManager();
$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();
$connection = Propel::getConnection();
$partnerId=100;
while($s = trim(fgets($f))){
        $sep = strpos($s, "\t") ? "\t" : " ";
        list($entryId, $plays, $views) = explode($sep, $s);
        myPartnerUtils::resetAllFilters();
        entryPeer::setDefaultCriteriaFilter();
        $entry = entryPeer::retrieveByPK ( $entryId);
        if (is_null ( $entry )) {
                KalturaLog::err ('Couldn\'t find entry [' . $entryId . ']' );
                continue;
        }
        if ($entry->getViews() != $views || $entry->getPlays() != $plays){
                $entry->setViews ( $views );
                $entry->setPlays ( $plays );
                KalturaLog::debug ( 'Successfully saved entry [' . $entryId . ']' );


		try {
			// update entry without setting the updated at
			$updateSql = "UPDATE entry set views='$views',plays='$plays' WHERE id='$entryId'";
			$stmt = $connection->prepare($updateSql);
			$stmt->execute();
			$affectedRows = $stmt->rowCount();
			KalturaLog::log("AffectedRows: ". $affectedRows);
			// update sphinx log directly
			$sql = $sphinxMgr->getSphinxSaveSql($entry, false);
			$sphinxLog = new SphinxLog();
			$sphinxLog->setEntryId($entryId);
			$sphinxLog->setPartnerId($partnerId);
			$sphinxLog->setSql($sql);
			$sphinxLog->save(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG));

		} catch (Exception $e) {
			KalturaLog::log($e->getMessage(), Propel::LOG_ERR);

		}
        }
        $count++;
	if ($count % 500 === 0){
	    entryPeer::clearInstancePool ();
	}
}
?>
