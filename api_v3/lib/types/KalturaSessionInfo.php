<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSessionInfo extends KalturaObject 
{
	/**
	 * @var string
	 * @readonly
	 */
	public $ks;

	/**
	 * @var KalturaSessionType
	 * @readonly
	 */
	public $sessionType;

	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var string
	 * @readonly
	 */
	public $userId;

	/**
	 * @var time expiry time in seconds (unix timestamp)
	 * @readonly
	 */
	public $expiry;

	/**
	 * @var string
	 * @readonly
	 */
	public $privileges;
}
