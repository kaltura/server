<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPropelUniqueValidator validates that the uniqueness of a column.
 * This validator only works for single column primary key.
 *
 * <b>Required parameters:</b>
 *
 * # <b>class</b>        - [none]               - Propel class name.
 * # <b>column</b>       - [none]               - Propel column name.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>unique_error</b> - [Uniqueness error]   - An error message to use when
 *                                                the value for this column already
 *                                                exists in the database.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fédéric Coelho <frederic.coelho@symfony-project.com>
 * @version    SVN: $Id: sfPropelUniqueValidator.class.php 2995 2006-12-09 18:01:32Z fabien $
 */
class sfPropelUniqueValidator extends sfValidator
{
  public function execute(&$value, &$error)
  {
    $className  = $this->getParameter('class').'Peer';
    $columnName = call_user_func(array($className, 'translateFieldName'), $this->getParameter('column'), BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);

    $c = new Criteria();
    $c->add($columnName, $value);
    $object = call_user_func(array($className, 'doSelectOne'), $c);

    if ($object)
    {
      $tableMap = call_user_func(array($className, 'getTableMap'));
      foreach ($tableMap->getColumns() as $column)
      {
        if (!$column->isPrimaryKey())
        {
          continue;
        }

        $method = 'get'.$column->getPhpName();
        $primaryKey = call_user_func(array($className, 'translateFieldName'), $column->getPhpName(), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
        if ($object->$method() != $this->getContext()->getRequest()->getParameter($primaryKey))
        {
          $error = $this->getParameter('unique_error');

          return false;
        }
      }
    }

    return true;
  }

  /**
   * Initialize this validator.
   *
   * @param sfContext The current application context.
   * @param array   An associative array of initialization parameters.
   *
   * @return bool true, if initialization completes successfully, otherwise false.
   */
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->setParameter('unique_error', 'Uniqueness error');

    $this->getParameterHolder()->add($parameters);

    // check parameters
    if (!$this->getParameter('class'))
    {
      throw new sfValidatorException('The "class" parameter is mandatory for the sfPropelUniqueValidator validator.');
    }

    if (!$this->getParameter('column'))
    {
      throw new sfValidatorException('The "column" parameter is mandatory for the sfPropelUniqueValidator validator.');
    }

    return true;
  }
}
