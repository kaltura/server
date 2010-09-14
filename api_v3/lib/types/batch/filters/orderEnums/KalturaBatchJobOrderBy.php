<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobOrderBy extends KalturaBaseJobOrderBy
{
	const STATUS_ASC = "+status";
	const STATUS_DESC = "-status";
	const QUEUE_TIME_ASC = "+queueTime";
	const QUEUE_TIME_DESC = "-queueTime";
	const FINISH_TIME_ASC = "+finishTime";
	const FINISH_TIME_DESC = "-finishTime";
}
