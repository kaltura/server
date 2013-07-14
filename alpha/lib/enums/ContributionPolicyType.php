<?php
/**
 * Who can assign entry to a specific category
 * @package Core
 * @subpackage model.enum
 */ 
interface ContributionPolicyType extends BaseEnum
{
	const ALL = 1;
	const MEMBERS_WITH_CONTRIBUTION_PERMISSION = 2;
}
