<?php
interface PermissionType extends BaseEnum
{
	const API_ACCESS       = '1';
	const SPECIAL_FEATURE  = '2';
	const PLUGIN           = '3';
	const EXTERNAL         = '99';
}