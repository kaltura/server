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
 * @version    SVN: $Id: sfPager.class.php 3099 2006-12-20 08:16:15Z fabien $
 */

/**
 *
 * sfPager class.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPager.class.php 3099 2006-12-20 08:16:15Z fabien $
 */
abstract class sfPager
{
  protected
    $page            = 1,
    $maxPerPage      = 0,
    $lastPage        = 1,
    $nbResults       = 0,
    $class           = '',
    $tableName       = '',
    $objects         = null,
    $cursor          = 1,
    $parameters      = array(),
    $currentMaxLink  = 1,
    $parameterHolder = null,
    $maxRecordLimit = false;

  public function __construct($class, $maxPerPage = 10)
  {
    $this->setClass($class);
    $this->setMaxPerPage($maxPerPage);
    $this->setPage(1);
    $this->parameterHolder = new sfParameterHolder();
  }

  // function to be called after parameters have been set
  abstract public function init( $cached_count = NULL );

  // main method: returns an array of result on the given page
  abstract public function getResults();

  // used internally by getCurrent()
  abstract protected function retrieveObject($offset);

  public function getCurrentMaxLink()
  {
    return $this->currentMaxLink;
  }

  public function getMaxRecordLimit()
  {
    return $this->maxRecordLimit;
  }

  public function setMaxRecordLimit($limit)
  {
    $this->maxRecordLimit = $limit;
  }

  public function getLinks($nb_links = 5)
  {
    $links = array();
    $tmp   = $this->page - floor($nb_links / 2);
    $check = $this->lastPage - $nb_links + 1;
    $limit = ($check > 0) ? $check : 1;
    $begin = ($tmp > 0) ? (($tmp > $limit) ? $limit : $tmp) : 1;

    $i = $begin;
    while (($i < $begin + $nb_links) && ($i <= $this->lastPage))
    {
      $links[] = $i++;
    }

    $this->currentMaxLink = $links[count($links) - 1];

    return $links;
  }

  public function haveToPaginate()
  {
    return (($this->getPage() != 0) && ($this->getNbResults() > $this->getMaxPerPage()));
  }

  public function getCursor()
  {
    return $this->cursor;
  }

  public function setCursor($pos)
  {
    if ($pos < 1)
    {
      $this->cursor = 1;
    }
    else if ($pos > $this->nbResults)
    {
      $this->cursor = $this->nbResults;
    }
    else
    {
      $this->cursor = $pos;
    }
  }

  public function getObjectByCursor($pos)
  {
    $this->setCursor($pos);

    return $this->getCurrent();
  }

  public function getCurrent()
  {
    return $this->retrieveObject($this->cursor);
  }

  public function getNext()
  {
    if (($this->cursor + 1) > $this->nbResults)
    {
      return null;
    }
    else
    {
      return $this->retrieveObject($this->cursor + 1);
    }
  }

  public function getPrevious()
  {
    if (($this->cursor - 1) < 1)
    {
      return null;
    }
    else
    {
      return $this->retrieveObject($this->cursor - 1);
    }
  }

  public function getFirstIndice()
  {
    if ($this->page == 0)
    {
      return 1;
    }
    else
    {
      return ($this->page - 1) * $this->maxPerPage + 1;
    }
  }

  public function getLastIndice()
  {
    if ($this->page == 0)
    {
      return $this->nbResults;
    }
    else
    {
      if (($this->page * $this->maxPerPage) >= $this->nbResults)
      {
        return $this->nbResults;
      }
      else
      {
        return ($this->page * $this->maxPerPage);
      }
    }
  }

  public function getCriteria()
  {
    return $this->criteria;
  }

  public function setCriteria($c)
  {
    $this->criteria = $c;
  }

  public function getClass()
  {
    return $this->class;
  }

  public function setClass($class)
  {
    $this->class = $class;
  }

  public function getNbResults()
  {
    return $this->nbResults;
  }

  protected function setNbResults($nb)
  {
    $this->nbResults = $nb;
  }

  public function getFirstPage()
  {
    return 1;
  }

  public function getLastPage()
  {
    return $this->lastPage;
  }

  protected function setLastPage($page)
  {
    $this->lastPage = $page;
    if ($this->getPage() > $page)
    {
      $this->setPage($page);
    }
  }

  public function getPage()
  {
    return $this->page;
  }

  public function getNextPage()
  {
    return min($this->getPage() + 1, $this->getLastPage());
  }

  public function getPreviousPage()
  {
    return max($this->getPage() - 1, $this->getFirstPage());
  }

  public function setPage($page)
  {
    $page = intval($page);

    $this->page = ($page <= 0) ? 1 : $page;
  }

  public function getMaxPerPage()
  {
    return $this->maxPerPage;
  }

  public function setMaxPerPage($max)
  {
    if ($max > 0)
    {
      $this->maxPerPage = $max;
      if ($this->page == 0)
      {
        $this->page = 1;
      }
    }
    else if ($max == 0)
    {
      $this->maxPerPage = 0;
      $this->page = 0;
    }
    else
    {
      $this->maxPerPage = 1;
      if ($this->page == 0)
      {
        $this->page = 1;
      }
    }
  }

  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  public function getParameter($name, $default = null, $ns = null)
  {
    return $this->parameterHolder->get($name, $default, $ns);
  }

  public function hasParameter($name, $ns = null)
  {
    return $this->parameterHolder->has($name, $ns);
  }

  public function setParameter($name, $value, $ns = null)
  {
    return $this->parameterHolder->set($name, $value, $ns);
  }
}
