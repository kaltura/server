<?php
/**
 * User Profile Micro Service
 */
class MicroServiceUserProfile extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct('user','user-profile');
	}

	/**
	 * List User Profiles
	 *
	 * @param $filter
	 * @param $pager
	 */
	public function list($partnerId, $filter, $pager = array())
	{
		return $this->serve($partnerId, 'list', array('filter' => $filter, 'pager' => $pager);
	}
}
