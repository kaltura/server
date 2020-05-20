<?php
chdir(dirname(__FILE__));
return;
require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();
if ($argc > 1 && is_numeric($argv[1]))
    $c->add(CaptionAssetItemPeer::CREATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(CaptionAssetItemPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(CaptionAssetItemPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
if($argc > 4)
	CaptionAssetItemPeer::setUseCriteriaFilter((bool)$argv[4]);
	
$c->addAscendingOrderByColumn(CaptionAssetItemPeer::CREATED_AT);
$c->addAscendingOrderByColumn(CaptionAssetItemPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$captions = CaptionAssetItemPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($captions))
{
	foreach($captions as $caption)
	{
		KalturaLog::log('caption_asset_id ' . $caption->getId() . ' int id[' . $caption->getIntId() . '] crc id[' . $sphinx->getSphinxId($caption) . '] last updated at ['. $caption->getUpdatedAt(null) .']');
		
		try {
			$ret = $sphinx->saveToSphinx($caption, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($captions));
	kMemoryManager::clearMemory();
	$captions = CaptionAssetItemPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Cureent time: ' . time());
exit(0);
