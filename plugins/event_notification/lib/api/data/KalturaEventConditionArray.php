<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 * @deprecated
 */
class KalturaEventConditionArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct("KalturaEventCondition");	
	}
}