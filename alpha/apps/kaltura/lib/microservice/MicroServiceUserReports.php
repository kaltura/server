<?php
/**
 * User Profile Micro Service
 * This represents the 'reports' service under 'plat-user' repo
 */
class MicroServiceUserReports extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::USER, MicroServiceService::REPORTS);
	}
}
