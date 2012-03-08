<?php

class kEntitlementUtils 
{

	public static $entitlementScope = false;  
	
	/**
	 * Returns true if kuser or current kuser is entitled to entryId
	 * @param string $entryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function isEntryEntitled($entryId, $kuserId = null)
	{
		//TODO
	} 	

	/**
	 * Returns true if kuser or current kuser is entitled to assign entry to categoryId
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function validateEntryAssignToCategory($categoryId, $kuserId = null)
	{ 
		//TODO
	}
	
	/**
	 * Set Entitlement scope - if entitelement is enabled \ disabled in this session
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function setEntitlementScope()
	{
		//TODO
		
		//TODO - RMOVE THIS CODE - FOR TESTS ONLY!
		self::$entitlementScope = true;
	}
	
	
}
