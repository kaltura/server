<?php


class kUltraSearchManager
{

	public static function doSearch(UltraSearchOperator $ultraSearchOperator)
	{
		$subQuery = kUltraQueryManager::createSearchQuery($ultraSearchOperator);
		/********************************************************
		 * Add all entitlement and other query stuff here
		 ******************************************************/
	}
}

?>