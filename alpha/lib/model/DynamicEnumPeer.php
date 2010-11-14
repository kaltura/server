<?php


/**
 * Skeleton subclass for performing query and update operations on the 'dynamic_enum' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class DynamicEnumPeer extends BaseDynamicEnumPeer {

	public static function alternativeCon($con)
	{
		if($con === null)
			$con = myDbHelper::alternativeCon($con);
			
		if($con === null)
			$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL3);
		
		return $con;
	}
	
	/**
	 * Retrieve a single object by its key names.
	 *
	 * @param      string $enumName the name of the enum class
	 * @param      string $valueName the name of the constant
	 * @param      string $pluginName the name of the plugin
	 * @param      PropelPDO $con the connection to use
	 * @return     DynamicEnum
	 */
	public static function retrieveByPluginConstant($enumName, $valueName, $pluginName, $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(DynamicEnumPeer::ENUM_NAME, $enumName);
		$criteria->add(DynamicEnumPeer::VALUE_NAME, $valueName);
		$criteria->add(DynamicEnumPeer::PLUGIN_NAME, $pluginName);

		return DynamicEnumPeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * Retrieve a single object by its value.
	 *
	 * @param      string $enumName the name of the enum class
	 * @param      int $value the name of the constant
	 * @param      string $pluginName the name of the plugin
	 * @param      PropelPDO $con the connection to use
	 * @return     DynamicEnum
	 */
	public static function retrieveByPluginValue($enumName, $value, $pluginName, $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(DynamicEnumPeer::ENUM_NAME, $enumName);
		$criteria->add(DynamicEnumPeer::VALUE, $value);
		$criteria->add(DynamicEnumPeer::PLUGIN_NAME, $pluginName);

		return DynamicEnumPeer::doSelectOne($criteria, $con);
	}
	
} // DynamicEnumPeer
