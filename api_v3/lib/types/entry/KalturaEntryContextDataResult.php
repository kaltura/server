<?php
/**
 * @package api
 * @subpackage objects
 */
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
	 * @var bool
	 */
	public $isIpAddressRestricted;
	
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