<?php

require_once 'propel/engine/builder/om/php5/PHP5ExtensionPeerBuilder.php';

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
 * @version    SVN: $Id: SfExtensionPeerBuilder.php 2624 2006-11-07 09:34:59Z fabien $
 */
class SfExtensionPeerBuilder extends PHP5ExtensionPeerBuilder
{
  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);
  }

  /**
   * Adds class phpdoc comment and openning of class.
   * @param string &$script The script will be modified in this method.
   */
  protected function addClassOpen(&$script)
  {
    $table = $this->getTable();
    $tableName = $table->getName();
    $tableDesc = $table->getDescription();

    $baseClassname = $this->getPeerBuilder()->getClassname();

    $script .= "
/**
 * Subclass for performing query and update operations on the '$tableName' table.
 *
 * $tableDesc
 *
 * @package ".$this->getPackage()."
 */ 
class ".$this->getClassname()." extends $baseClassname
{";
  }

  /**
   * Closes class.
   * @param string &$script The script will be modified in this method.
   */ 
  protected function addClassClose(&$script)
  {
    $script .= "
}
";
  }
}
