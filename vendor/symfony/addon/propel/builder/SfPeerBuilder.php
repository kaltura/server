<?php

require_once 'propel/engine/builder/om/php5/PHP5ComplexPeerBuilder.php';

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
 * @version    SVN: $Id: SfPeerBuilder.php 2534 2006-10-26 17:13:50Z fabien $
 */
class SfPeerBuilder extends PHP5ComplexPeerBuilder
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

  protected function addSelectMethods(&$script)
  {
    parent::addSelectMethods($script);

    if ($this->getTable()->getAttribute('isI18N'))
    {
      $this->addDoSelectWithI18n($script);
    }
  }

  protected function addDoSelectWithI18n(&$script)
  {
    $table = $this->getTable();
    $thisTableObjectBuilder = OMBuilder::getNewObjectBuilder($table);
    $className = $table->getPhpName();
    $pks = $table->getPrimaryKey();
    $pk = PeerBuilder::getColumnName($pks[0], $className);

    // get i18n table name and culture column name
    foreach ($table->getReferrers() as $fk)
    {
      $tblFK = $fk->getTable();
      if ($tblFK->getName() == $table->getAttribute('i18nTable'))
      {
        $i18nClassName = $tblFK->getPhpName();
        // FIXME
        $i18nPeerClassName = $i18nClassName.'Peer';

        $i18nTable = $table->getDatabase()->getTable($tblFK->getName());
        $i18nTableObjectBuilder = OMBuilder::getNewObjectBuilder($i18nTable);
        $i18nTablePeerBuilder = OMBuilder::getNewPeerBuilder($i18nTable);
        $i18nPks = $i18nTable->getPrimaryKey();
        $i18nPk = PeerBuilder::getColumnName($i18nPks[0], $i18nClassName);

        $culturePhpName = '';
        $cultureColumnName = '';
        foreach ($tblFK->getColumns() as $col)
        {
          if (("true" === strtolower($col->getAttribute('isCulture'))))
          {
            $culturePhpName = $col->getPhpName();
            $cultureColumnName = PeerBuilder::getColumnName($col, $i18nClassName);
          }
        }
      }
    }

    $script .= "

  /**
   * Selects a collection of $className objects pre-filled with their i18n objects.
   *
   * @return array Array of $className objects.
   * @throws PropelException Any exceptions caught during processing will be
   *     rethrown wrapped into a PropelException.
   */
  public static function doSelectWithI18n(Criteria \$c, \$culture = null, \$con = null)
  {
    if (\$culture === null)
    {
      \$culture = sfContext::getInstance()->getUser()->getCulture();
    }

    // Set the correct dbName if it has not been overridden
    if (\$c->getDbName() == Propel::getDefaultDB())
    {
      \$c->setDbName(self::DATABASE_NAME);
    }

    ".$this->getPeerClassname()."::addSelectColumns(\$c);
    \$startcol = (".$this->getPeerClassname()."::NUM_COLUMNS - ".$this->getPeerClassname()."::NUM_LAZY_LOAD_COLUMNS) + 1;

    ".$i18nPeerClassName."::addSelectColumns(\$c);

    \$c->addJoin(".$pk.", ".$i18nPk.");
    \$c->add(".$cultureColumnName.", \$culture);

    \$rs = ".$this->basePeerClassname."::doSelect(\$c, \$con);
    \$results = array();

    while(\$rs->next()) {
";
            if ($table->getChildrenColumn()) {
              $script .= "
      \$omClass = ".$this->getPeerClassname()."::getOMClass(\$rs, 1);
";
            } else {
              $script .= "
      \$omClass = ".$this->getPeerClassname()."::getOMClass();
";
            }
            $script .= "
      \$cls = Propel::import(\$omClass);
      \$obj1 = new \$cls();
      \$obj1->hydrate(\$rs);
      \$obj1->setCulture(\$culture);
";
//            if ($i18nTable->getChildrenColumn()) {
              $script .= "
      \$omClass = ".$i18nTablePeerBuilder->getPeerClassname()."::getOMClass(\$rs, \$startcol);
";
//            } else {
//              $script .= "
//      \$omClass = ".$i18nTablePeerBuilder->getPeerClassname()."::getOMClass();
//";
//            }

            $script .= "
      \$cls = Propel::import(\$omClass);
      \$obj2 = new \$cls();
      \$obj2->hydrate(\$rs, \$startcol);

      \$obj1->set".$i18nClassName."ForCulture(\$obj2, \$culture);
      \$obj2->set".$className."(\$obj1);

      \$results[] = \$obj1;
    }
    return \$results;
  }
";
  }

  protected function addDoValidate(&$script)
  {
      $tmp = '';
      parent::addDoValidate($tmp);

      $script .= str_replace("return {$this->basePeerClassname}::doValidate(".$this->getPeerClassname()."::DATABASE_NAME, ".$this->getPeerClassname()."::TABLE_NAME, \$columns);\n",
        "\$res =  {$this->basePeerClassname}::doValidate(".$this->getPeerClassname()."::DATABASE_NAME, ".$this->getPeerClassname()."::TABLE_NAME, \$columns);\n".
        "    if (\$res !== true) {\n".
        "        \$request = sfContext::getInstance()->getRequest();\n".
        "        foreach (\$res as \$failed) {\n".
        "            \$col = ".$this->getPeerClassname()."::translateFieldname(\$failed->getColumn(), BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);\n".
        "            \$request->setError(\$col, \$failed->getMessage());\n".
        "        }\n".
        "    }\n\n".
        "    return \$res;\n", $tmp);
  }

  protected function addDoSelectRS(&$script)
  {
    $tmp = '';
    parent::addDoSelectRS($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:addDoSelectRS:addDoSelectRS') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";
      $tmp = preg_replace('/{/', '{'.$mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoUpdate(&$script)
  {
    $tmp = '';
    parent::addDoUpdate($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doUpdate:pre') as \$callable)
    {
      \$ret = call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con);
      if (false !== \$ret)
      {
        return \$ret;
      }
    }

";

      $post_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doUpdate:post') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con, \$ret);
    }

    return \$ret;
";

      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace("/\t\treturn ([^}]+)/", "\t\t\$ret = $1".$post_mixer_script.'  ', $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoInsert(&$script)
  {
    $tmp = '';
    parent::addDoInsert($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doInsert:pre') as \$callable)
    {
      \$ret = call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con);
      if (false !== \$ret)
      {
        return \$ret;
      }
    }

";

      $post_mixer_script = "
    foreach (sfMixer::getCallables('{$this->getClassname()}:doInsert:post') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con, \$pk);
    }

    return";

      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace("/\t\treturn/", "\t\t".$post_mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }
}
