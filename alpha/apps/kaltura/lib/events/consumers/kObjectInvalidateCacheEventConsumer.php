<?php
/**
 * @package Core
 * @subpackage events
 */
interface kObjectInvalidateCacheEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param $object
	 * @param $params
	 * @return bool true if should continue to the next consumer
	 */
	public function invalidateCache($object ,$params = null);

	/**
	 * @param $object
	 * @param $params
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeInvalidateCache($object, $params = null);
}
