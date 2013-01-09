<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface accessControlContextType extends BaseEnum
{
	const PLAY = 1;
	const DOWNLOAD = 2;
	const THUMBNAIL = 3;
	const METADATA = 4;
}
