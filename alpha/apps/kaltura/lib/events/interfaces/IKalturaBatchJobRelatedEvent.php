<?php
/**
 * Interface denoting an event which in a scope that is related to a batch job object.
 *
 * @package Core
 * @subpackage events
 */
interface IKalturaBatchJobRelatedEvent
{
	/**
	 * Method returns the batch job object in the context of which the event occurred.
	 * @return BatchJob
	 */
	public function getBatchJob();
}