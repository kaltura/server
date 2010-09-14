<?php
class KalturaEntryContextDataResult extends KalturaObject
{
	/**
	 * @var bool
	 */
	public $isSiteRestricted;
	
	/**
	 * @var bool
	 */
	public $isCountryRestricted;
	
	/**
	 * @var bool
	 */
	public $isSessionRestricted;
	
	/**
	 * @var int
	 */
	public $previewLength;
	
	/**
	 * @var bool
	 */
	public $isScheduledNow;
	
	/**
	 * @var bool
	 */
	public $isAdmin;
}