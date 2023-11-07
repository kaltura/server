<?php
/**
 * Base User Micro Services Class
 */
abstract class MicroServiceUser extends MicroServiceBaseService
{
	public function __construct($serviceName)
	{
		parent::__construct('user', $serviceName);
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
