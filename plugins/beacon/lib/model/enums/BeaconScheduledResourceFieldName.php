<?php
/**
 * @package plugins.beacon
 * @subpackage model.enum
 */
interface BeaconScheduledResourceFieldName extends BaseEnum
{
	const STATUS = 'private_data.Errors.APP_STATUS';
	const RECORDING = 'private_data.RecordingData.recordingPhase';
	const RESOURCE_NAME = 'private_data.ResourceDetails.resourceName';
	const UPDATED_AT = 'updated_at';
	const EVENT_TYPE = 'event_type';
	const OBJECT_ID = 'object_id';
	const IS_LOG = 'is_log';
	const PARTNER_ID = 'partner_id';
}
