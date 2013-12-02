<?php

abstract class KObjectTaskEntryEngineBase extends KObjectTaskEngineBase
{
	function getSupportedObjectTypes()
	{
		return array('KalturaBaseEntry');
	}
} 