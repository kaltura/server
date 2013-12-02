<?php

class KObjectTaskDeleteEntryEngine extends KObjectTaskEntryEngineBase
{
	function processObject($object)
	{
		/** @var KalturaBaseEntry $object */
		$client = $this->getClient();
		$client->baseEntry->delete($object->id);
	}
}