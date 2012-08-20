<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelBehavior.class.php 2453 2006-10-20 05:58:48Z fabien $
 */
class sfPropelBehavior
{
  static protected $behaviors = array();

  static public function registerMethods($name, $callables)
  {
    if (!isset(self::$behaviors[$name]))
    {
      self::$behaviors[$name] = array('methods' => array(), 'hooks' => array());
    }
    foreach ($callables as $callable)
    {
      self::$behaviors[$name]['methods'][] = $callable;
    }
  }

  static public function registerHooks($name, $hooks)
  {
    if (!isset(self::$behaviors[$name]))
    {
      self::$behaviors[$name] = array('methods' => array(), 'hooks' => array());
    }
    foreach ($hooks as $hook => $callable)
    {
      if (!isset(self::$behaviors[$name]['hooks']))
      {
        self::$behaviors[$name]['hooks'][$hook] = array();
      }

      self::$behaviors[$name]['hooks'][$hook][] = $callable;
    }
  }

  static public function add($class, $behaviors)
  {
    foreach ($behaviors as $name => $parameters)
    {
      if (is_int($name))
      {
        // no parameters
        $name = $parameters;
      }
      else
      {
        // register parameters
        foreach ($parameters as $key => $value)
        {
          sfConfig::set('propel_behavior_'.$name.'_'.$class.'_'.$key, $value);
        }
      }

      if (!isset(self::$behaviors[$name]))
      {
        throw new sfConfigurationException(sprintf('Propel behavior "%s" is not registered', $name));
      }

      // register hooks
      foreach (self::$behaviors[$name]['hooks'] as $hook => $callables)
      {
        foreach ($callables as $callable)
        {
          sfMixer::register('Base'.$class.$hook, $callable);
        }
      }

      // register new methods
      foreach (self::$behaviors[$name]['methods'] as $callable)
      {
        sfMixer::register('Base'.$class, $callable);
      }
    }
  }
}
