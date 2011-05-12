<?php

require_once 'propel/engine/builder/om/php5/PHP5MapBuilderBuilder.php';

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
 * @version    SVN: $Id: SfMapBuilderBuilder.php 3058 2006-12-16 17:17:26Z fabien $
 */
class SfMapBuilderBuilder extends PHP5MapBuilderBuilder
{
  public function build()
  {
    if (!DataModelBuilder::getBuildProperty('builderAddComments'))
    {
      return sfToolkit::stripComments(parent::build());
    }
    
    return parent::build();
  }

  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);
  }

  protected function addDoBuild(&$script)
  {
    parent::addDoBuild($script);

    // fix http://propel.phpdb.org/trac/ticket/235: Column sizes not being inserted into [table]MapBuilder->DoBuild() by PHP5MapBuilderBuilder
    $sizes = array();
    foreach ($this->getTable()->getColumns() as $col)
    {
      $sizes[$col->getPhpName()] = !$col->getSize() ? 'null' : $col->getSize();
    }
    $script = preg_replace("/\\\$tMap\->addColumn\('([^']+)', '([^']+)', '([^']+)', CreoleTypes\:\:VARCHAR, (false|true)\)/e", '"\\\$tMap->addColumn(\'$1\', \'$2\', \'$3\', CreoleTypes::VARCHAR, $4, {$sizes[\'$2\']})"', $script);
  }
}
