<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface PrivacyType extends BaseEnum
{
	const ALL = 1;
	const AUTHENTICATED_USERS = 2;
	const MEMBERS_ONLY = 3;
}
