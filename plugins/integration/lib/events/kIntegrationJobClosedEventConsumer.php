<?php
/**
 * @package plugins.integration
 * @subpackage lib.events
 */
interface kIntegrationJobClosedEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BatchJob $batchJob
	 */
	public function shouldConsumeIntegrationCloseEvent(BatchJob $batchJob);
	
	/**
	 * @param BatchJob $batchJob
	 */
	public function integrationJobClosed(BatchJob $batchJob);
}
