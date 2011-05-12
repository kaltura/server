<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfGenerator is the abstract base class for all generators.
 *
 * @package    symfony
 * @subpackage database
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id $
 */
class sfPropelDataRetriever
{
  static public function retrieveObjects($class, $peerMethod = null)
  {
    if (!$classPath = sfCore::getClassPath($class.'Peer'))
    {
      throw new sfException(sprintf('Unable to find path for class "%s".', $class.'Peer'));
    }

    require_once($classPath);

    if (!$peerMethod)
    {
      $peerMethod = 'doSelect';
    }

    $classPeer = $class.'Peer';

    if (!is_callable(array($classPeer, $peerMethod)))
    {
      throw new sfException(sprintf('Peer method "%s" not found for class "%s"', $peerMethod, $classPeer));
    }

    $objects = call_user_func(array($classPeer, $peerMethod), new Criteria());

    return $objects;
  }
}