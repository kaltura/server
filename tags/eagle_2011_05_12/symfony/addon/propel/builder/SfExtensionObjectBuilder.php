<?php

require_once 'propel/engine/builder/om/php5/PHP5ExtensionObjectBuilder.php';

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
 * @version    SVN: $Id: SfExtensionObjectBuilder.php 2624 2006-11-07 09:34:59Z fabien $
 */
class SfExtensionObjectBuilder extends PHP5ExtensionObjectBuilder
{
  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);
  }

  protected function addClassOpen(&$script)
  {
    $table = $this->getTable();
    $tableName = $table->getName();
    $tableDesc = $table->getDescription();

    $baseClassname = $this->getObjectBuilder()->getClassname();

    $script .= "
/**
 * Subclass for representing a row from the '$tableName' table.
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
