<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface kPackagerUrlType extends BaseEnum
{
	const REGULAR = 1;
	const MAPPED = 2;
	const REMOTE = 3;
	const LOCAL_LIVE = 4;
}
