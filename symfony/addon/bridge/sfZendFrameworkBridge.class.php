<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (sfConfig::get('sf_zend_lib_dir'))
{
  set_include_path(sfConfig::get('sf_zend_lib_dir').PATH_SEPARATOR.get_include_path());
  require_once(sfConfig::get('sf_zend_lib_dir').'/Zend.php');
}
else
{
  require_once('Zend.php');
}

/**
 * This class makes easy to use Zend Framework classes within symfony
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfZendFrameworkBridge.class.php 1415 2006-06-11 08:33:51Z fabien $
 */
class sfZendFrameworkBridge
{
  public static function autoload($class)
  {
    try
    {
      Zend::loadClass($class);
    }
    catch (Zend_Exception $e)
    {
      return false;
    }

    return true;
  }
}
