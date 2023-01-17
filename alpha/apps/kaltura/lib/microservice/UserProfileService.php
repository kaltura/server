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
	public function list($partnerId, $filter, $pager = [])
	{
		return $this->serve($partnerId, 'list', ['filter' => $filter, 'pager' => $pager]);
	}
}
