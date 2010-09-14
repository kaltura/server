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
 * @version    SVN: $Id: sfPropelPager.class.php 2728 2006-11-17 09:46:01Z chtito $
 */

/**
 *
 * sfPropelPager class.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelPager.class.php 2728 2006-11-17 09:46:01Z chtito $
 */
class kPropelPager extends sfPager
{
  protected
    $criteria               = null,
    $peer_method_name       = 'doSelect',
    $peer_count_method_name = 'doCount';

  public function __construct($class, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);

    $this->setCriteria(new Criteria());
    $this->tableName = constant($class.'Peer::TABLE_NAME');
  }
  
  public function init( $cached_count = NULL )
  {
    $hasMaxRecordLimit = ($this->getMaxRecordLimit() !== false);
    $maxRecordLimit = $this->getMaxRecordLimit();

    $cForCount = clone $this->getCriteria();
    $cForCount->setOffset(0);
    $cForCount->setLimit(0);
    $cForCount->clearGroupByColumns();

    // require the model class (because autoloading can crash under some conditions)
//    if (!$classPath = sfCore::getClassPath($this->getClassPeer()))
//    {
//      throw new sfException(sprintf('Unable to find path for class "%s".', $this->getClassPeer()));
//    }
//    require_once($classPath);
    if ( $cached_count != NULL )
    {
    	$count = $cached_count;
    }
    else
    {
    	$count = call_user_func(array($this->getClassPeer(), $this->getPeerCountMethod()), $cForCount);
    }


    $this->setNbResults($hasMaxRecordLimit ? min($count, $maxRecordLimit) : $count);

    $c = $this->getCriteria();
    $c->setOffset(0);
    $c->setLimit(0);

    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0))
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
      $c->setOffset($offset);

      if ($hasMaxRecordLimit)
      {
        $maxRecordLimit = $maxRecordLimit - $offset;
        if ($maxRecordLimit > $this->getMaxPerPage())
        {
          $c->setLimit($this->getMaxPerPage());
        }
        else
        {
          $c->setLimit($maxRecordLimit);
        }
      }
      else
      {
        $c->setLimit($this->getMaxPerPage());
      }
    }
  }

  protected function retrieveObject($offset)
  {
    $cForRetrieve = clone $this->getCriteria();
    $cForRetrieve->setOffset($offset - 1);
    $cForRetrieve->setLimit(1);

    $results = call_user_func(array($this->getClassPeer(), $this->getPeerMethod()), $cForRetrieve);

    return $results[0];
  }

  public function getResults()
  {
    $c = $this->getCriteria();

    return call_user_func(array($this->getClassPeer(), $this->getPeerMethod()), $c);
  }

  public function getPeerMethod()
  {
    return $this->peer_method_name;
  }

  public function setPeerMethod($peer_method_name)
  {
    $this->peer_method_name = $peer_method_name;
  }

  public function getPeerCountMethod()
  {
    return $this->peer_count_method_name;
  }

  public function setPeerCountMethod($peer_count_method_name)
  {
    $this->peer_count_method_name = $peer_count_method_name;
  }
  
  public function getClassPeer()
  {
    return $this->class.'Peer';
  }
}
