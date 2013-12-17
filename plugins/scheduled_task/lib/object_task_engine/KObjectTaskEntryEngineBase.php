<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectTaskEngine
 */
abstract class KObjectTaskEntryEngineBase extends KObjectTaskEngineBase
{
	function getSupportedObjectTypes()
	{
		return array('KalturaBaseEntry');
	}
} 