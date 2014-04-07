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

		$this->aggregateEntryStatistics( self::$taskConfig->params );
	}

	private function aggregateEntryStatistics( $params )
	{
		$aggregatorConfigurationEnumerator = new AggregatorConfigurationEnumerator( $params );
		$configs = $aggregatorConfigurationEnumerator->getConfigurations();
		$entryStatisticsAggregator = new EntryStatisticsAggregator();
		foreach ( $configs as $config )
		{
			try {
				$entryStatisticsAggregator->run( $config );
			}
			catch ( Exception $e ) {
				KalturaLog::err( $e ); // Log and continue to the next
			}
		}
	}
}
