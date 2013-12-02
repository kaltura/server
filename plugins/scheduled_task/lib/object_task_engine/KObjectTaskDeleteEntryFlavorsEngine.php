<?php

class KObjectTaskDeleteEntryFlavorsEngine extends KObjectTaskEntryEngineBase
{
	function processObject($object)
	{
		/** @var KalturaBaseEntry $object */
		$client = $this->getClient();
	}
}