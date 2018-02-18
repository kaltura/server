<?php
/**
 * will encapsulate methods for connectiong to more than one DB 
 */
class myDbHelper
{
	const DB_HELPER_CONN_MASTER = "propel";
	const DB_HELPER_CONN_PROPEL2 = "propel2";
	const DB_HELPER_CONN_PROPEL3 = "propel3";
	const DB_HELPER_CONN_SPHINX_LOG = "sphinx_log";
	const DB_HELPER_CONN_SPHINX_LOG_READ = "sphinx_log_read";
	const DB_HELPER_CONN_DWH = "dwh";
	
	public static $use_alternative_con = null;
	protected static $slaveConnIndex = false;
	
	/**
	 * @param string $name
	 * @return PDO
	 */
	public static function getConnection($name)
	{
		if(!Propel::isInit())
		{
			DbManager::setConfig(kConf::getDB());
			DbManager::initialize();
		}
		
		$slaves = array(self::DB_HELPER_CONN_PROPEL2, self::DB_HELPER_CONN_PROPEL3);
		if (!in_array($name, $slaves))
			return Propel::getConnection($name);
		
		list($connection, self::$slaveConnIndex) = DbManager::connectFallbackLogic(
				array('Propel', 'getConnection'),
				array(),
				$slaves, 
				self::$slaveConnIndex);
		if (!$connection)
			throw new PropelException('Could not connect to any database server');
			
		return $connection;
	}
	
	public static function alternativeCon ( $con )
	{
		// create a connection to the set alternative connection
		// NOTE: if the con already exists or if the alternative connection is null the given connection will be returned.
		// only null connection will be overriden
		if ( $con === null && self::$use_alternative_con )
		{
			$con = self::getConnection ( self::$use_alternative_con);
		}
		
		return $con;
	}
	
	
	public static function dbShutdown ()
	{
		$databaseManager = new sfDatabaseManager();
		$databaseManager->shutdown();
		Propel::close();
	}
}
