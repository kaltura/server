<?php
class KalturaAuditTrailChangeItem extends KalturaObject
{
	/**
	 * @var string
	 */
	public $descriptor;
	
	/**
	 * @var string
	 */
	public $oldValue;
	
	/**
	 * @var string
	 */
	public $newValue;
}
