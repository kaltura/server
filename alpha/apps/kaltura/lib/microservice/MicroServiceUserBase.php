<?php
/**
 * Base User Micro Services Class
 * This is the base class for all services under 'plat-user' repo
 */
abstract class MicroServiceUserBase extends MicroServiceBaseService
{
	public static $host = 'user';
	
	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}
	
	/**
	 * List User Profiles
	 *
	 * @param $filter
	 * @param $pager
	 */
	public function list($partnerId, $filter, $pager = array())
	{
		return $this->serve($partnerId, 'list', array('filter' => $filter, 'pager' => $pager));
	}
}
