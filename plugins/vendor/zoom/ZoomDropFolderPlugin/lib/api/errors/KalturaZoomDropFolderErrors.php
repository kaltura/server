<?php

/**
 * @package plugins.vendor
 * @subpackage api.errors
 */
class KalturaZoomDropFolderErrors extends KalturaErrors
{
	const DROP_FOLDER_INTEGRATION_DATA_MISSING = 'DROP_FOLDER_INTEGRATION_DATA_MISSING;;Missing integration data for this drop folder';
	const EXCEEDED_MAX_ZOOM_DROP_FOLDERS = 'EXCEEDED_MAX_ZOOM_DROP_FOLDERS;;Amount of maximum zoom drop folders per partner exceeded';
}