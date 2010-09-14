<?php
/*
 *  $Id: Creole.php,v 1.14 2006/01/17 20:06:31 hlellelid Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

include_once 'creole/SQLException.php';
include_once 'creole/Connection.php';

// static:
// track errors is used by drivers to get better error messages
// make sure it's set.

@ini_set('track_errors', true);

/**
 * This is the class that manages the database drivers.
 *
 * There are a number of default drivers (at the time of writing this comment: MySQL, MSSQL, SQLite, PgSQL, Oracle)
 * that are "shipped" with Creole.  You may wish to either add a new driver or swap out one of the existing drivers
 * for your own custom driver.  To do this you simply need to register your driver using the registerDriver() method.
 *
 * Note that you register your Connection class because the Connection class is responsible for calling the other
 * driver classes (e.g. ResultSet, PreparedStatement, etc.).
 *
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.14 $
 * @package   creole
 */
class Creole {

    /**
     * Constant that indicates a connection object should be used.
     */
    const PERSISTENT = 1;

    /**
     * Flag to pass to the connection to indicate that no case conversions
     * should be performed by ResultSet on keys of fetched rows.
	 * @deprecated use COMPAT_ASSOC_LOWER
     */
    const NO_ASSOC_LOWER = 16;
	
    /**
     * Flag to pass to the connection to indicate that a to-lower case conversion
     * should be performed by ResultSet on keys of fetched rows.
     */
	const COMPAT_ASSOC_LOWER = 32;

    /**
     * Flag to pass to the connection to indicate that an rtrim() should be performed
	 * on strings (using ResultSet->getString(), etc.).
     */
	const COMPAT_RTRIM_STRING = 64;
	
	/**
	 * Flag to indicate that all compatibility flags should be set.
	 */
	const COMPAT_ALL = 96;
	
    /**
     * Map of built-in drivers.
     * Change or add your own using registerDriver()
     * @see registerDriver()
     * @var array Hash mapping phptype => driver class (in dot-path notation, e.g. 'mysql' => 'creole.drivers.mysql.MySQLConnection').
     */
    private static $driverMap = array(  'mysql' => 'creole.drivers.mysql.MySQLConnection',
                                        'mysqli' => 'creole.drivers.mysqli.MySQLiConnection',
                                        'pgsql' => 'creole.drivers.pgsql.PgSQLConnection',
                                        'sqlite' => 'creole.drivers.sqlite.SQLiteConnection',
                                        'oracle' => 'creole.drivers.oracle.OCI8Connection',
                                        'mssql' => 'creole.drivers.mssql.MSSQLConnection',
                                        'odbc' => 'creole.drivers.odbc.ODBCConnection'
                                       );

    /**
     * Map of already established connections
     * @see getConnection()
     * @var array Hash mapping connection DSN => Connection instance
     */
    private static $connectionMap = array();

    /**
     * Register your own RDBMS driver class.
     *
     * You can use this to specify your own class that replaces a default driver or
     * adds support for a new driver.  Register your own class by specifying the
     * 'phptype' (e.g. mysql) and a dot-path notation to where your Connection class is
     * relative to any location on the include path.  You can also specify '*' as the phptype
     * if you want to register a driver that will handle any native type (e.g. if creating
     * a set of decorator classes that log SQL before calling native driver methods).  YOU CAN
     * ONLY REGISTER ONE CATCHALL ('*') DRIVER.
     * <p>
     * Note: the class you need to register is your Connection class because this is the
     * class that's responsible for instantiating the other classes that are part of your
     * driver.  It is possible to mix & match drivers -- i.e. to write a custom driver where
     * the Connection object just instantiates stock classes for ResultSet and PreparedStatement.
     * Note that if you wanted to "override" only the ResultSet class you would also have to override
     * the Connection and PreparedStatement classes so that they would return the correct ResultSet
     * class.  In the future we may implement a more "packaged" approach to drivers; for now we
     * want to keep it simple.
     *
     * @param string $phptype   The phptype (mysql, mssql, etc.). This is first part of DSN URL (e.g. mysql://localhost/...).
     *                          You may also specify '*' to register a driver that will "wrap" the any native drivers.
     * @param string $dotpath   A dot-path locating your class.  For example 'creole.drivers.mssql.MSSQLConnection'
     *                          will be included like: include 'creole/drivers/mssql/MSSQLConnection.php' and the
     *                          classname will be assumed to be 'MSSQLConnection'.
     * @return void
     */
    public static function registerDriver($phptype, $dotpath)
    {
        self::$driverMap[$phptype] = $dotpath;
    }

    /**
     * Removes the driver for a PHP type.  Note that this will remove user-registered
     * drivers _and_ the default drivers.
     * @param string $phptype The PHP type for driver to de-register.
     * @see registerDriver()
     */
    public static function deregisterDriver($phptype)
    {
        unset(self::$driverMap[$phptype]);
    }

    /**
     * Returns the class path to the driver registered for specified type.
     * @param string $phptype The phptype handled by driver (e.g. 'mysql', 'mssql', '*').
     * @return string The driver class in dot-path notation (e.g. creole.drivers.mssql.MSSQLConnection)
     *                  or NULL if no registered driver found.
     */
    public static function getDriver($phptype)
    {
        if (isset(self::$driverMap[$phptype])) {
            return self::$driverMap[$phptype];
        } else {
            return null;
        }
    }

    /**
     * Create a new DB connection object and connect to the specified
     * database
     *
     * @param mixed $dsn "data source name", see the self::parseDSN
     * method for a description of the dsn format.  Can also be
     * specified as an array of the format returned by DB::parseDSN().

     * @param int $flags Connection flags (e.g. PERSISTENT).
     *
     * @return Connection Newly created DB connection object
     * @throws SQLException
     * @see self::parseDSN()
     */
    public static function getConnection($dsn, $flags = 0)
    {
        if (is_array($dsn)) {
            $dsninfo = $dsn;
        } else {
            $dsninfo = self::parseDSN($dsn);
        }
		
		// gather any flags from the DSN
		if ( isset ( $dsninfo['persistent'] ) && ! empty ( $dsninfo['persistent'] ) )
			$flags |= Creole::PERSISTENT;
		if ( isset ( $dsninfo['compat_assoc_lower'] ) && ! empty ( $dsninfo['compat_assoc_lower'] ) )
			$flags |= Creole::COMPAT_ASSOC_LOWER;
		if ( isset ( $dsninfo['compat_rtrim_string'] ) && ! empty ( $dsninfo['compat_rtrim_string'] ) )
			$flags |= Creole::COMPAT_RTRIM_STRING;
		if ( isset ( $dsninfo['compat_all'] ) && ! empty ( $dsninfo['compat_all'] ) )
			$flags |= Creole::COMPAT_ALL;
		
		if ($flags & Creole::NO_ASSOC_LOWER) {
			trigger_error("The Creole::NO_ASSOC_LOWER flag has been deprecated, and is now the default behavior. Use Creole::COMPAT_ASSOC_LOWER to lowercase resulset keys.", E_USER_WARNING);
		}

        // sort $dsninfo by keys so the serialized result is always the same
        // for identical connection parameters, no matter what their order is
        ksort($dsninfo);
        $connectionMapKey = crc32(serialize($dsninfo + array('compat_flags' => ($flags & Creole::COMPAT_ALL))));

        // see if we already have a connection with these parameters cached
        if(isset(self::$connectionMap[$connectionMapKey]))
        {
            // persistent connections will be used if a non-persistent one was requested and is available
            // but a persistent connection will be created if a non-persistent one is present

	    // TODO: impliment auto close of non persistent and replacing the
	    // non persistent with the persistent object so as we dont have
	    // both links open for no reason

            if( isset(self::$connectionMap[$connectionMapKey][1]) ) { // is persistent
                // a persistent connection with these parameters is already there,
                // so we return it, no matter what was specified as persistent flag
                $con = self::$connectionMap[$connectionMapKey][1];
            } else {
                // we don't have a persistent connection, and since the persistent
                // flag wasn't set either, we just return the non-persistent connection
                $con = self::$connectionMap[$connectionMapKey][0];
            }

            // if we're here, a non-persistent connection was already there, but
            // the user wants a persistent one, so it will be created
            
            if ($con->isConnected())
                return $con;            
        }

        // support "catchall" drivers which will themselves handle the details of connecting
        // using the proper RDBMS driver.
        if (isset(self::$driverMap['*'])) {
            $type = '*';
        } else {
            $type = $dsninfo['phptype'];
            if (!isset(self::$driverMap[$type])) {
                throw new SQLException("No driver has been registered to handle connection type: $type");
            }
        }

        // may need to make this more complex if we add support
        // for 'dbsyntax'
        $clazz = self::import(self::$driverMap[$type]);
        $obj = new $clazz();

        if (!($obj instanceof Connection)) {
            throw new SQLException("Class does not implement creole.Connection interface: $clazz");
        }

        try {
            $obj->connect($dsninfo, $flags);
        } catch(SQLException $sqle) {
            $sqle->setUserInfo($dsninfo);
            throw $sqle;
        }
		$persistent = ($flags & Creole::PERSISTENT) === Creole::PERSISTENT;
        return self::$connectionMap[$connectionMapKey][(int)$persistent] = $obj;
    }

    /**
     * Parse a data source name.
     *
     * This isn't quite as powerful as DB::parseDSN(); it's also a lot simpler, a lot faster,
     * and many fewer lines of code.
     *
     * A array with the following keys will be returned:
     *  phptype: Database backend used in PHP (mysql, odbc etc.)
     *  protocol: Communication protocol to use (tcp, unix etc.)
     *  hostspec: Host specification (hostname[:port])
     *  database: Database to use on the DBMS server
     *  username: User name for login
     *  password: Password for login
     *
     * The format of the supplied DSN is in its fullest form:
     *
     *  phptype://username:password@protocol+hostspec/database
     *
     * Most variations are allowed:
     *
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype
     *
     * @param string $dsn Data Source Name to be parsed
     * @return array An associative array
     */
    public static function parseDSN($dsn)
    {
        if (is_array($dsn)) {
            return $dsn;
        }

        $parsed = array(
            'phptype'  => null,
            'username' => null,
            'password' => null,
            'protocol' => null,
            'hostspec' => null,
            'port'     => null,
            'socket'   => null,
            'database' => null
        );

        $info = parse_url($dsn);

        if (count($info) === 1) { // if there's only one element in result, then it must be the phptype
            $parsed['phptype'] = array_pop($info);
            return $parsed;
        }

        // some values can be copied directly
        $parsed['phptype'] = @$info['scheme'];
        $parsed['username'] = @$info['user'];
        $parsed['password'] = @$info['pass'];
        $parsed['port'] = @$info['port'];

        $host = @$info['host'];
        if (false !== ($pluspos = strpos($host, '+'))) {
            $parsed['protocol'] = substr($host,0,$pluspos);
            if ($parsed['protocol'] === 'unix') {
                $parsed['socket'] = substr($host,$pluspos+1);
            } else {
                $parsed['hostspec'] = substr($host,$pluspos+1);
            }
        } else {
            $parsed['hostspec'] = $host;
        }

        if (isset($info['path'])) {
            $parsed['database'] = substr($info['path'], 1); // remove first char, which is '/'
        }

        if (isset($info['query'])) {
                $opts = explode('&', $info['query']);
                foreach ($opts as $opt) {
                    list($key, $value) = explode('=', $opt);
                    if (!isset($parsed[$key])) { // don't allow params overwrite
                        $parsed[$key] = urldecode($value);
                    }
                }
        }

        return $parsed;
    }

    /**
     * Include once a file specified in DOT notation.
     * Package notation is expected to be relative to a location
     * on the PHP include_path.
     * @param string $class
     * @return string unqualified classname
     * @throws SQLException - if class does not exist and cannot load file
     *                      - if after loading file class still does not exist
     */
    public static function import($class) {
        $pos = strrpos($class, '.');
        // get just classname ('path.to.ClassName' -> 'ClassName')
        if ($pos !== false) {
            $classname = substr($class, $pos + 1);
        }
        else
        {
          $classname = $class;
        }
        if (!class_exists($classname, false)) {
            $path = strtr($class, '.', DIRECTORY_SEPARATOR) . '.php';
            $ret = include_once($path);
            if ($ret === false) {
                throw new SQLException("Unable to load driver class: " . $class);
            }
            if (!class_exists($classname)) {
                throw new SQLException("Unable to find loaded class: $classname (Hint: make sure classname matches filename)");
            }
        }
        return $classname;
    }

}
