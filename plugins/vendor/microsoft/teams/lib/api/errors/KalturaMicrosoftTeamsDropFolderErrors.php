<?php

/**
 * @package plugins.vendor
 * @subpackage api.errors
 */
class KalturaMicrosoftTeamsDropFolderErrors extends KalturaErrors
{
	const DROP_FOLDER_INTEGRATION_DATA_MISSING = 'DROP_FOLDER_INTEGRATION_DATA_MISSING;;Missing integration data for this drop folder';
	const EXCEEDED_MAX_TEAMS_DROP_FOLDERS = 'EXCEEDED_MAX_TEAMS_DROP_FOLDERS;;Amount of maximum teams drop folders per partner exceeded';
}