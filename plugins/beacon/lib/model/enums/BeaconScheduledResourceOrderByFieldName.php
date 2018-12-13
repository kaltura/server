<?php
/**
 * @package plugins.beacon
 * @subpackage model.enum
 */
interface BeaconScheduledResourceOrderByFieldName extends BaseEnum
{
	const STATUS = 'private_data.Errors.APP_STATUS';
	const RECORDING = 'private_data.RecordingData.recordingPhase';
	const RESOURCE_NAME = 'private_data.ResourceDetails.resourceName.raw';
	const UPDATED_AT = 'updated_at';
}
