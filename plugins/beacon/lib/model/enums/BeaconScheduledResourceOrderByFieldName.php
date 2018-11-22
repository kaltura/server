<?php
/**
 * @package plugins.beacon
 * @subpackage model.enum
 */
interface BeaconScheduledResourceOrderByFieldName extends BaseEnum
{
	const STATUS = 'private_data.Errors.APP_STATUS.keyword';
	const RECORDING = 'private_data.RecordingData.recordingPhase.keyword';
	const RESOURCE_NAME = 'private_data.ResourceDetails.resourceName.keyword';
	const UPDATED_AT = 'updated_at';
}
