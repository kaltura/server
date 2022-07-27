<?php

/**
 * @package plugins.kafkaNotification
 * @subpackage model.enum
 */
interface KafkaNotificationFormat extends BaseEnum
{
	const JSON = 1;
	const AVRO = 2;
}