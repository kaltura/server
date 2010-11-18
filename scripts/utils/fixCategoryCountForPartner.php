<?php
ini_set("memory_limit","256M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting ( E_ALL );

$dbConf = kConf::getDB ();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();
$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

$c = new Criteria();
$c->add(categoryPeer::PARTNER_ID, $partner_id);
$c->add(categoryPeer::DELETED_AT, null);
$c->setLimit(200);
$categories = categoryPeer::doSelect($c, $con);

echo "number of categories:".count($categories)."\n";
foreach($categories as $category)
{
	$category->setEntriesCount(0);
	$category->save();
	echo "Set 0 count to category ".$category->getId()."\n";	
}

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
//$c->setLimit(20);
$entries = entryPeer::doSelect($c);

foreach($entries as $entry)
{

	$categoriesIds = $entry->getCategoriesIds();
	$categoriesIdsArr = explode(',',$categoriesIds);
	
	foreach($categoriesIdsArr as $categoryId)
	{
		categoryPeer::clearInstancePool();
		
		$c = new Criteria();
		$c->add(categoryPeer::PARTNER_ID, $partner_id);
		$c->add(categoryPeer::DELETED_AT, null);
		$c->add(categoryPeer::ID,$categoryId);
		$c->setLimit(200);
		$categories = categoryPeer::doSelect($c, $con);
		
		if(count($categories) > 1)
		{
			echo "error when selecting category [$categoryId], entry [".$entry->getId()."], returned more then one category\n";
			continue;
		}else if(count($categories) == 0){
			//no categories for entry
			continue;
		}else {
			echo "increase entries count category id [$categoryId], entry [".$entry->getId()."]\n";
			$categories[0]->incrementEntriesCount();
			$categories[0]->save();
			
		}
	}

}

echo 'Done';
