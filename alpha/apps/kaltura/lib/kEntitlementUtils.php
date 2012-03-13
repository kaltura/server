<?php
/**
 * kEntitlementUtils is all utils needed for entitlement use cases.
 * @package Core
 * @subpackage utils
 *
 */
class kEntitlementUtils 
{

	protected static $entitlementScope = false;  
	
	
	public static function getEntitlementScope()
	{
		return self::$entitlementScope;
	}
	
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
	public static function initEntitlementScope()
	{
		//TODO - RMOVE THIS CODE - FOR TESTS ONLY!
		self::$entitlementScope = true;
		
		return true;
	}
	
	
}
