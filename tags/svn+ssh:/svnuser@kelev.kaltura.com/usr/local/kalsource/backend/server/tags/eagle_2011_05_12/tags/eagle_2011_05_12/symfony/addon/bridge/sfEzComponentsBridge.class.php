<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once((sfConfig::get('sf_ez_lib_dir') ? sfConfig::get('sf_ez_lib_dir').'/' : '').'Base/src/base.php');

/**
 * This class makes easy to use ez components classes within symfony
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfEzComponentsBridge.class.php 1415 2006-06-11 08:33:51Z fabien $
 */
class sfEzComponentsBridge
{
  public static function autoload($class)
  {
    return ezcBase::autoload($class);
  }
}
