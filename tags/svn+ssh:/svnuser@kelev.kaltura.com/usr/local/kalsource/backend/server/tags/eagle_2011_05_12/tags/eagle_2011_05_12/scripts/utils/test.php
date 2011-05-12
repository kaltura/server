<?php

set_time_limit(0);
ini_set("memory_limit","700M");
error_reporting(E_ALL);
chdir(dirname(__FILE__));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();


$createdAt = time() - (60 * 60 * 24);	
$jobType = 0;

		$c = new Criteria();
//		$c->add(BatchJobPeer::CREATED_AT, $createdAt, Criteria::GREATER_THAN);
		$c->add(BatchJobPeer::JOB_TYPE, $jobType);
//		$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_PENDING);
		$c->clearSelectColumns();
		$c->addSelectColumn('MAX(' . BatchJobPeer::PRIORITY . ')');
		$stmt = BatchJobPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$maxPriority = $stmt->fetchColumn();
		
		var_dump($maxPriority);