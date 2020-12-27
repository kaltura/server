<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface kPackagerUrlType extends BaseEnum
{
	const REGULAR_THUMB = 1;
	const MAPPED_THUMB = 2;
	const REMOTE_THUMB = 3;
	const LOCAL_LIVE_THUMB = 4;
	const REGULAR_VOLUME_MAP = 5;
	const MAPPED_VOLUME_MAP = 6;
	const REMOTE_VOLUME_MAP = 7;
}
