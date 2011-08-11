<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A symfony database driver for Propel, derived from the native Creole driver.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>datasource</b>     - [symfony] - datasource to use for the connection
 * # <b>is_default</b>     - [false]   - use as default if multiple connections
 *                                       are specified. The parameters
 *                                       that has been flagged using this param
 *                                       is be used when Propel is initialized
 *                                       via sfPropelAutoload.
 *
 * @package    symfony
 * @subpackage database
 *
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelDatabase.class.php 3493 2007-02-18 09:23:10Z fabien $
 */
class sfPropelDatabase extends sfCreoleDatabase
{
  static protected
    $config = array();

  public function initialize($parameters = null, $name = 'propel')
  {
    parent::initialize($parameters);

    if (!$this->hasParameter('datasource'))
    {
      $this->setParameter('datasource', $name);
    }

    $this->addConfig();

    $is_default = $this->getParameter('is_default', false);

    // first defined if none listed as default
    if ($is_default || count(self::$config['propel']['datasources']) == 1)
    {
      $this->setDefaultConfig();
    }
  }

  public function setDefaultConfig()
  {
    self::$config['propel']['datasources']['default'] = $this->getParameter('datasource');
  }

  public function addConfig()
  {
    if ($this->hasParameter('host'))
    {
      $this->setParameter('hostspec', $this->getParameter('host'));
    }

    if ($dsn = $this->getParameter('dsn'))
    {
      require_once('creole/Creole.php');
      $params = Creole::parseDSN($dsn);

      $options = array('phptype', 'hostspec', 'database', 'username', 'password', 'port', 'protocol', 'encoding', 'persistent');
      foreach ($options as $option)
      {
        if (!$this->getParameter($option) && isset($params[$option]))
        {
          $this->setParameter($option, $params[$option]);
        }
      }
    }

    self::$config['propel']['datasources'][$this->getParameter('datasource')] =
      array(
        'adapter'      => $this->getParameter('phptype'),
        'connection'   =>
        array(
          'phptype'    => $this->getParameter('phptype'),
          'hostspec'   => $this->getParameter('hostspec'),
          'database'   => $this->getParameter('database'),
          'username'   => $this->getParameter('username'),
          'password'   => $this->getParameter('password'),
          'port'       => $this->getParameter('port'),
          'encoding'   => $this->getParameter('encoding'),
          'persistent' => $this->getParameter('persistent'),
          'protocol'   => $this->getParameter('protocol'),
        ),
      );
  }

  public static function getConfiguration()
  {
    return self::$config;
  }

  public function setConnectionParameter($key, $value)
  {
    if ($key == 'host')
    {
      $key = 'hostspec';
    }

    self::$config['propel']['datasources'][$this->getParameter('datasource')]['connection'][$key] = $value;
    $this->setParameter($key, $value);
  }
}
