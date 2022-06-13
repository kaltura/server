<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */
interface kRecordingFileType extends BaseEnum
{
	const UNDEFINED = 0;
	const VIDEO = 1;
	const CHAT = 2;
	const TRANSCRIPT = 3;
	const AUDIO = 4;
	const CC = 5;
}