<?php
/**
 * @package Scheduler
 * @subpackage Statistics
 */

require_once __DIR__ . '/Aggregator/AggregatorConfigurationEnumerator.php';
require_once __DIR__ . '/Aggregator/EntryStatisticsAggregator.php';

/**
 * Run periodically and aggregate entries statistics according to a set of rules
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncEntryStatistics extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::ENTRY_STATSISTICS;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		KalturaLog::info("EntryStatistics is running");

		$dbHost = $this->getAdditionalParams("dbHost");
		$dbPort = $this->getAdditionalParams("dbPort");
		$dbName = $this->getAdditionalParams("dbName");
		$dbTableName = $this->getAdditionalParams("dbTableName");

		$this->aggregateEntryStatistics( $dbHost, $dbPort, $dbName, $dbTableName );
	}

	private function aggregateEntryStatistics( $dbHost, $dbPort, $dbName, $dbTableName )
	{
		$aggregatorConfigurationEnumerator = new AggregatorConfigurationEnumerator( $dbHost, $dbPort, $dbName, $dbTableName );
		$configs = $aggregatorConfigurationEnumerator->getConfigurations();
		$entryStatisticsAggregator = new EntryStatisticsAggregator();
		foreach ( $configs as $config )
		{
			$entryStatisticsAggregator->run( $config );
		}
	}
}
