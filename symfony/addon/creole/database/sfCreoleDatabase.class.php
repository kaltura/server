<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfCreoleDatabase provides connectivity for the Creole database abstraction
 * layer.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>classpath</b>      - [none]   - An absolute filesystem path to the main
 *                                      Creole class file.
 * # <b>database</b>       - [none]   - The database name.
 * # <b>dsn</b>            - [none]   - The DSN formatted connection string.
 * # <b>host</b>           - [none]   - The database host specifications.
 * # <b>port</b>           - [none]   - The database port.
 * # <b>encoding</b>       - [none]   - The database encoding.
 * # <b>method</b>         - [normal] - How to read connection parameters.
 *                                      Possible values are dsn, normal,
 *                                      server, and env. The dsn method reads
 *                                      them from the dsn parameter. The
 *                                      normal method reads them from the
 *                                      specified values. server reads them
 *                                      from $_SERVER where the keys to
 *                                      retrieve the values are what you
 *                                      specify the value as in the settings.
 *                                      env reads them from $_ENV and works
 *                                      like $_SERVER.
 * # <b>no_assoc_lower</b> - [Off]    - Turn off portabilty of resultset
 *                                      field names.
 * # <b>password</b>       - [none]   - The database password.
 * # <b>persistent</b>     - [No]     - Indicates that the connection should
 *                                      persistent.
 * # <b>phptype</b>        - [none]   - The type of database (mysql, pgsql,
 *                                      etc).
 * # <b>username</b>       - [none]   - The database username.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfCreoleDatabase.class.php 3329 2007-01-23 08:29:34Z fabien $
 */
class sfCreoleDatabase extends sfDatabase
{
  /**
   * Connect to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created.
   */
  public function connect()
  {
    try
    {
      // determine how to get our settings
      $method = $this->getParameter('method', 'normal');

      switch ($method)
      {
        case 'normal':
          // get parameters normally, and all are required
          $database = $this->getParameter('database', null);
          $hostspec = $this->getParameter('hostspec') ? $this->getParameter('hostspec') : ($this->getParameter('host') ? $this->getParameter('hostspec') : null);
          $password = $this->getParameter('password', null);
          $phptype  = $this->getParameter('phptype',  null);
          $username = $this->getParameter('username', null);
          $port     = $this->getParameter('port',     null);
          $encoding = $this->getParameter('encoding', null);

          $dsn = array('database' => $database,
                       'hostspec' => $hostspec,
                       'password' => $password,
                       'phptype'  => $phptype,
                       'username' => $username,
                       'port'     => $port,
                       'encoding' => $encoding);
          break;

        case 'dsn':
          $dsn = $this->getParameter('dsn');

          if ($dsn == null)
          {
            // missing required dsn parameter
            $error = 'Database configuration specifies method "dsn", but is missing dsn parameter';

            throw new sfDatabaseException($error);
          }

          break;

        case 'server':
          // construct a DSN connection string from existing $_SERVER values
          $dsn =& $this->loadDSN($_SERVER);

          break;

        case 'env':
          // construct a DSN connection string from existing $_ENV values
          $dsn =& $this->loadDSN($_ENV);

          break;

        default:
          // who knows what the user wants...
          $error = 'Invalid CreoleDatabase parameter retrieval method "%s"';
          $error = sprintf($error, $method);

          throw new sfDatabaseException($error);
      }

      // get creole class path
      $classPath = $this->getParameter('classpath');

      // include the creole file
      if ($classPath == null)
      {
        require_once('creole/Creole.php');
      }
      else
      {
        require_once($classPath);
      }

      // set our flags
      $noAssocLower = $this->getParameter('no_assoc_lower', false);
      $persistent   = $this->getParameter('persistent', false);
      $compatAssocLower  = $this->getParameter('compat_assoc_lower', false);
      $compatRtrimString = $this->getParameter('compat_rtrim_string', false);

      $flags  = 0;
      $flags |= ($noAssocLower)      ? Creole::NO_ASSOC_LOWER : 0;
      $flags |= ($persistent)        ? Creole::PERSISTENT : 0;
      $flags |= ($compatAssocLower)  ? Creole::COMPAT_ASSOC_LOWER : 0;
      $flags |= ($compatRtrimString) ? Creole::COMPAT_RTRIM_STRING : 0;

      // do the duuuurtay work, right thurr
      if ($flags > 0)
      {
        $this->connection = Creole::getConnection($dsn, $flags);
      }
      else
      {
        $this->connection = Creole::getConnection($dsn);
      }

      // get our resource
      $this->resource = $this->connection->getResource();
    }
    catch (SQLException $e)
    {
      // the connection's foobar'd
      throw new sfDatabaseException($e->toString());
    }
  }

  /**
   * Load a DSN connection string from an existing array.
   *
   * @return array An associative array of connection parameters.
   */
  protected function & loadDSN(&$array)
  {
    // determine if a dsn is set, otherwise use separate parameters
    $dsn = $this->getParameter('dsn');

    if ($dsn == null)
    {
      // list of available parameters
      $available = array('database', 'hostspec', 'password', 'phptype', 'username', 'port');

      $dsn = array();

      // yes, i know variable variables are ugly, but let's avoid using
      // an array for array's sake in this single spot in the source
      foreach ($available as $parameter)
      {
        $$parameter = $this->getParameter($parameter);

        $dsn[$parameter] = ($$parameter != null) ? $array[$$parameter] : null;
      }
    }
    else
    {
      $dsn = $array[$dsn];
    }

    return $dsn;
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database.
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      @$this->connection->close();
    }
  }
}
