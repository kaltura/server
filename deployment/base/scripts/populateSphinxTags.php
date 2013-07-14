<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(TagPeer::ID, $argv[1], Criteria::GREATER_EQUAL);

if($argc > 2 && is_numeric($argv[2]))
	$c->add(TagPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
	
if($argc > 3)
	TagPeer::setUseCriteriaFilter((bool)$argv[3]);
	
$c->addAscendingOrderByColumn(TagPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$tags = TagPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($tags))
{
	foreach($tags as $tag)
	{
	    /* @var $tag Tag */
		KalturaLog::log('tag id ' . $tag->getId() . ' tag string [' . $tag->getTag() . '] crc id[' . $sphinx->getSphinxId($tag) . ']');
		
		try {
			$ret = $sphinx->saveToSphinx($tag, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($tags));
	kMemoryManager::clearMemory();
	$tags = TagPeer::doSelect($c, $con);
}

KalturaLog::log('Done');
exit(0);
