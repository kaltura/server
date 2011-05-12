<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class can be used to cache the result and output of functions/methods.
 *
 * This class is based on the PEAR_Cache_Lite class.
 * All cache files are stored in files in the [sf_root_dir].'/cache/'.[sf_app].'/function' directory.
 * To disable all caching, you can set to false [sf_cache] constant.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fabien Marty <fab@php.net>
 * @version    SVN: $Id: sfFunctionCache.class.php 3452 2007-02-14 15:03:08Z francois $
 */
class sfFunctionCache extends sfFileCache
{
  /**
   * Calls a cacheable function or method (or not if there is already a cache for it).
   *
   * Arguments of this method are read with func_get_args. So it doesn't appear in the function definition. Synopsis : 
   * call('functionName', $arg1, $arg2, ...)
   * (arg1, arg2... are arguments of 'functionName')
   *
   * @return mixed The result of the function/method
   */
  public function call()
  {
    $arguments = func_get_args();

    // Generate a cache id
    $id = md5(serialize($arguments));

    $data = $this->get($id);
    if ($data !== null)
    {
      $array = unserialize($data);
      $output = $array['output'];
      $result = $array['result'];
    }
    else
    {
      $target = array_shift($arguments);
      ob_start();
      ob_implicit_flush(false);
      if (is_string($target) && strstr($target, '::'))
      {
        // classname::staticMethod
        list($class, $method) = explode('::', $target);
        try
        {
          $result = call_user_func_array(array($class, $method), $arguments);
        }
        catch (Exception $e)
        {
          ob_end_clean();
          throw $e;
        }
      }
      else if (is_string($target) && strstr($target, '->'))
      {
        // object->method
        // use a stupid name ($objet_123456789 because) of problems when the object
        // name is the same as this var name
        list($object_123456789, $method) = explode('->', $target);
        global $$object_123456789;
        try
        {
          $result = call_user_func_array(array($$object_123456789, $method), $arguments);
        }
        catch (Exception $e)
        {
          ob_end_clean();
          throw $e;
        }
      }
      else
      {
        // function
        $result = call_user_func_array($target, $arguments);
      }
      $output = ob_get_contents();
      ob_end_clean();

      $array['output'] = $output;
      $array['result'] = $result;

      $this->set($id, '', serialize($array));
    }

    echo($output);
    return $result;
  }
}
