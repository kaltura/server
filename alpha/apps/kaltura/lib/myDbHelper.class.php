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
	const DB_HELPER_CONN_THUMB = "thumbs_sql";
	const DB_HELPER_CONN_DWH = "dwh";
	
	public static $use_alternative_con = null;
	
	/**
	 * @param string $name
	 * @return PDO
	 */
	public static function getConnection($name)
	{
		return Propel::getConnection ($name);
	}
	
	public static function alternativeCon ( $con )
	{
		// create a connection to the set alternative connection
		// NOTE: if the con already exists or if the alternative connection is null the given connection will be returned.
		// only null connection will be overriden
		if ( $con === null && self::$use_alternative_con )
		{
			$con = Propel::getConnection ( self::$use_alternative_con);
		}
		
		return $con;
	}
	
	
	public static function dbShutdown ()
	{
		$databaseManager = new sfDatabaseManager();
		$databaseManager->shutdown();
		propel::close();
	}
}
?>