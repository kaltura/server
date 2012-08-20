<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfComponents.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfComponents.class.php 1415 2006-06-11 08:33:51Z fabien $
 */
abstract class sfComponents extends sfComponent
{
  public function execute()
  {
    throw new sfInitializationException('sfComponents initialization failed');
  }
}
