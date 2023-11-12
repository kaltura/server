<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface MicroServiceService extends BaseEnum
{
	//app-registry host
	//app-registry service
	const APP_REGISTRY = 'app-registry';
	
	// auth host
	// app-subscription service
	const APP_SUBSCRIPTION = 'app-subscription';
	
	// auth-manager service
	const AUTH_MANAGER = 'auth-manager';
	
	// auth-profile service
	const AUTH_PROFILE = 'auth-profile';
	
	// spa-proxy service
	const SPA_PROXY = 'spa-proxy';
	
	// user host
	// user-profile service
	const USER_PROFILE = 'user-profile';
	
	// reports service
	CONST REPORTS = 'reports';
}
