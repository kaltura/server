<?php

ini_set("memory_limit","256M");

define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/../../alpha/'));
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$c = new Criteria();
$c->add(BatchJobPeer::STATUS, BatchJob::BATCHJOB_STATUS_FINISHED, Criteria::NOT_IN);
$c->add(BatchJobPeer::JOB_TYPE, BatchJob::BATCHJOB_TYPE_STORAGE_EXPORT);
$c->add(BatchJobPeer::JOB_SUB_TYPE, 0);
$c->setLimit(10);

$jobs = BatchJobPeer::doSelect($c);
if(!count($jobs))
{
	die('no jobs found');
}

foreach($jobs as $job)
{
	$data = $job->getData();
	if(!($data instanceof kStorageExportJobData))
	{
		echo "Job [" . $job->getId() . "] has invalid data\n";
		continue;
	}
	if($job->getDc() == 0)
		$data->setServerUrl('ny-www.kaltura.com');
	else
		$data->setServerUrl('pa-www.kaltura.com');
	
	$fileSync = FileSyncPeer::retrieveByPK($data->getSrcFileSyncId());
	if($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_READY)
	{
		$job->setStatus(BatchJob::BATCHJOB_STATUS_FINISHED);
		echo "FileSync [" . $data->getSrcFileSyncId() . "] ready\n";
	}
	else
	{
		$job->setData($data);
		$job->setStatus(BatchJob::BATCHJOB_STATUS_RETRY);
		$job->setExecutionAttempts(0);
		$job->setSchedulerId(null);
		$job->setWorkerId(null);
		$job->setBatchIndex(null);
		echo "FileSync [" . $data->getSrcFileSyncId() . "] pending\n";
	}
	$job->save();
	echo "Job [" . $job->getId() . "] saved\n";
}

echo 'Done';
