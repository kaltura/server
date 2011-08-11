[?php

/**
 * <?php echo $this->getGeneratedModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getGeneratedModuleName() ?>

 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 3501 2007-02-18 10:28:17Z fabien $
 */
class <?php echo $this->getGeneratedModuleName() ?>Actions extends sfActions
{
  public function executeIndex()
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'list');
  }

  public function executeList()
  {
    $this->processSort();

    $this->processFilters();

<?php if ($this->getParameterValue('list.filters')): ?>
    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/<?php echo $this->getSingularName() ?>/filters');
<?php endif ?>

    // pager
    $this->pager = new sfPropelPager('<?php echo $this->getClassName() ?>', <?php echo $this->getParameterValue('list.max_per_page', 20) ?>);
    $c = new Criteria();
    $this->addSortCriteria($c);
    $this->addFiltersCriteria($c);
    $this->pager->setCriteria($c);
    $this->pager->setPage($this->getRequestParameter('page', 1));
<?php if ($this->getParameterValue('list.peer_method')): ?>
    $this->pager->setPeerMethod('<?php echo $this->getParameterValue('list.peer_method') ?>');
<?php endif ?>
<?php if ($this->getParameterValue('list.peer_count_method')): ?>
    $this->pager->setPeerCountMethod('<?php echo $this->getParameterValue('list.peer_count_method') ?>');
<?php endif ?>
    $this->pager->init();
  }

  public function executeCreate()
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'edit');
  }

  public function executeSave()
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'edit');
  }

  public function executeEdit()
  {
    $this-><?php echo $this->getSingularName() ?> = $this->get<?php echo $this->getClassName() ?>OrCreate();

    if ($this->getRequest()->getMethod() == sfRequest::POST)
    {
      $this->update<?php echo $this->getClassName() ?>FromRequest();

      $this->save<?php echo $this->getClassName() ?>($this-><?php echo $this->getSingularName() ?>);

      $this->setFlash('notice', 'Your modifications have been saved');

      if ($this->getRequestParameter('save_and_add'))
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/create');
      }
      else if ($this->getRequestParameter('save_and_list'))
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/list');
      }
      else
      {
        return $this->redirect('<?php echo $this->getModuleName() ?>/edit?<?php echo $this->getPrimaryKeyUrlParams('this->') ?>);
      }
    }
    else
    {
      $this->labels = $this->getLabels();
    }
  }

  public function executeDelete()
  {
    $this-><?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(40) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);

    try
    {
      $this->delete<?php echo $this->getClassName() ?>($this-><?php echo $this->getSingularName() ?>);
    }
    catch (PropelException $e)
    {
      $this->getRequest()->setError('delete', 'Could not delete the selected <?php echo sfInflector::humanize($this->getSingularName()) ?>. Make sure it does not have any associated items.');
      return $this->forward('<?php echo $this->getModuleName() ?>', 'list');
    }

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php $upload_dir = $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.upload_dir')) ?>
      $currentFile = sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$this-><?php echo $this->getSingularName() ?>->get<?php echo $column->getPhpName() ?>();
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }

<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
    return $this->redirect('<?php echo $this->getModuleName() ?>/list');
  }

  public function handleErrorEdit()
  {
    $this->preExecute();
    $this-><?php echo $this->getSingularName() ?> = $this->get<?php echo $this->getClassName() ?>OrCreate();
    $this->update<?php echo $this->getClassName() ?>FromRequest();

    $this->labels = $this->getLabels();

    return sfView::SUCCESS;
  }

  protected function save<?php echo $this->getClassName() ?>($<?php echo $this->getSingularName() ?>)
  {
    $<?php echo $this->getSingularName() ?>->save();

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): $type = $column->getCreoleType(); ?>
<?php $name = $column->getName() ?>
<?php if ($column->isPrimaryKey()) continue ?>
<?php $credentials = $this->getParameterValue('edit.fields.'.$column->getName().'.credentials') ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php

$user_params = $this->getParameterValue('edit.fields.'.$column->getName().'.params');
$user_params = is_array($user_params) ? $user_params : sfToolkit::stringToArray($user_params);
$through_class = isset($user_params['through_class']) ? $user_params['through_class'] : '';

?>
<?php if ($through_class): ?>
<?php

$class = $this->getClassName();
$related_class = sfPropelManyToMany::getRelatedClass($class, $through_class);
$related_table = constant($related_class.'Peer::TABLE_NAME');
$middle_table = constant($through_class.'Peer::TABLE_NAME');
$this_table = constant($class.'Peer::TABLE_NAME');

$related_column = sfPropelManyToMany::getRelatedColumn($class, $through_class);
$column = sfPropelManyToMany::getColumn($class, $through_class);

?>
<?php if ($input_type == 'admin_double_list' || $input_type == 'admin_check_list' || $input_type == 'admin_select_list'): ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    if ($this->getUser()->hasCredential(<?php echo $credentials ?>))
    {
<?php endif; ?>
      // Update many-to-many for "<?php echo $name ?>"
      $c = new Criteria();
      $c->add(<?php echo $through_class ?>Peer::<?php echo strtoupper($column->getColumnName()) ?>, $<?php echo $this->getSingularName() ?>->getPrimaryKey());
      <?php echo $through_class ?>Peer::doDelete($c);

      $ids = $this->getRequestParameter('associated_<?php echo $name ?>');
      if (is_array($ids))
      {
        foreach ($ids as $id)
        {
          $<?php echo ucfirst($through_class) ?> = new <?php echo $through_class ?>();
          $<?php echo ucfirst($through_class) ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>->getPrimaryKey());
          $<?php echo ucfirst($through_class) ?>->set<?php echo $related_column->getPhpName() ?>($id);
          $<?php echo ucfirst($through_class) ?>->save();
        }
      }

<?php if ($credentials): ?>
    }
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
  }

  protected function delete<?php echo $this->getClassName() ?>($<?php echo $this->getSingularName() ?>)
  {
    $<?php echo $this->getSingularName() ?>->delete();
  }

  protected function update<?php echo $this->getClassName() ?>FromRequest()
  {
    $<?php echo $this->getSingularName() ?> = $this->getRequestParameter('<?php echo $this->getSingularName() ?>');

<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): $type = $column->getCreoleType(); ?>
<?php $name = $column->getName() ?>
<?php if ($column->isPrimaryKey()) continue ?>
<?php $credentials = $this->getParameterValue('edit.fields.'.$column->getName().'.credentials') ?>
<?php $input_type = $this->getParameterValue('edit.fields.'.$column->getName().'.type') ?>
<?php if ($credentials): $credentials = str_replace("\n", ' ', var_export($credentials, true)) ?>
    if ($this->getUser()->hasCredential(<?php echo $credentials ?>))
    {
<?php endif; ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php $upload_dir = $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.upload_dir')) ?>
    $currentFile = sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$this-><?php echo $this->getSingularName() ?>->get<?php echo $column->getPhpName() ?>();
    if (!$this->getRequest()->hasErrors() && isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>_remove']))
    {
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>('');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
    }

    if (!$this->getRequest()->hasErrors() && $this->getRequest()->getFileSize('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]'))
    {
<?php elseif ($type != CreoleTypes::BOOLEAN): ?>
    if (isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']))
    {
<?php endif; ?>
<?php if ($input_type == 'admin_input_file_tag'): ?>
<?php if ($this->getParameterValue('edit.fields.'.$column->getName().'.filename')): ?>
      $fileName = "<?php echo str_replace('"', '\\"', $this->replaceConstants($this->getParameterValue('edit.fields.'.$column->getName().'.filename'))) ?>";
<?php else: ?>
      $fileName = md5($this->getRequest()->getFileName('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]').time().rand(0, 99999));
<?php endif ?>
      $ext = $this->getRequest()->getFileExtension('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]');
      if (is_file($currentFile))
      {
        unlink($currentFile);
      }
      $this->getRequest()->moveFile('<?php echo $this->getSingularName() ?>[<?php echo $name ?>]', sfConfig::get('sf_upload_dir')."/<?php echo $upload_dir ?>/".$fileName.$ext);
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($fileName.$ext);
<?php elseif ($type == CreoleTypes::DATE || $type == CreoleTypes::TIMESTAMP): ?>
      if ($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'])
      {
        try
        {
          $dateFormat = new sfDateFormat($this->getUser()->getCulture());
          <?php $inputPattern  = $type == CreoleTypes::DATE ? 'd' : 'g'; ?>
          <?php $outputPattern = $type == CreoleTypes::DATE ? 'i' : 'I'; ?>
          if (!is_array($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']))
          {
            $value = $dateFormat->format($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'], '<?php echo $outputPattern ?>', $dateFormat->getInputPattern('<?php echo $inputPattern ?>'));
          }
          else
          {
            $value_array = $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'];
            $value = $value_array['year'].'-'.$value_array['month'].'-'.$value_array['day'].(isset($value_array['hour']) ? ' '.$value_array['hour'].':'.$value_array['minute'].(isset($value_array['second']) ? ':'.$value_array['second'] : '') : '');
          }
          $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($value);
        }
        catch (sfException $e)
        {
          // not a date
        }
      }
      else
      {
        $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>(null);
      }
<?php elseif ($type == CreoleTypes::BOOLEAN): ?>
    $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>(isset($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']) ? $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] : 0);
<?php elseif ($column->isForeignKey()): ?>
    $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] ? $<?php echo $this->getSingularName() ?>['<?php echo $name ?>'] : null);
<?php else: ?>
      $this-><?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($<?php echo $this->getSingularName() ?>['<?php echo $name ?>']);
<?php endif; ?>
<?php if ($type != CreoleTypes::BOOLEAN): ?>
    }
<?php endif; ?>
<?php if ($credentials): ?>
      }
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
  }

  protected function get<?php echo $this->getClassName() ?>OrCreate(<?php echo $this->getMethodParamsForGetOrCreate() ?>)
  {
    if (<?php echo $this->getTestPksForGetOrCreate() ?>)
    {
      $<?php echo $this->getSingularName() ?> = new <?php echo $this->getClassName() ?>();
    }
    else
    {
      $<?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForGetOrCreate() ?>);

      $this->forward404Unless($<?php echo $this->getSingularName() ?>);
    }

    return $<?php echo $this->getSingularName() ?>;
  }

  protected function processFilters()
  {
<?php if ($this->getParameterValue('list.filters')): ?>
    if ($this->getRequest()->hasParameter('filter'))
    {
      $filters = $this->getRequestParameter('filters');
<?php foreach ($this->getColumns('list.filters') as $column): $type = $column->getCreoleType() ?>
<?php if ($type == CreoleTypes::DATE || $type == CreoleTypes::TIMESTAMP): ?>
      if (isset($filters['<?php echo $column->getName() ?>']['from']) && $filters['<?php echo $column->getName() ?>']['from'] !== '')
      {
        $filters['<?php echo $column->getName() ?>']['from'] = sfI18N::getTimestampForCulture($filters['<?php echo $column->getName() ?>']['from'], $this->getUser()->getCulture());
      }
      if (isset($filters['<?php echo $column->getName() ?>']['to']) && $filters['<?php echo $column->getName() ?>']['to'] !== '')
      {
        $filters['<?php echo $column->getName() ?>']['to'] = sfI18N::getTimestampForCulture($filters['<?php echo $column->getName() ?>']['to'], $this->getUser()->getCulture());
      }
<?php endif; ?>
<?php endforeach; ?>

      $this->getUser()->getAttributeHolder()->removeNamespace('sf_admin/<?php echo $this->getSingularName() ?>/filters');
      $this->getUser()->getAttributeHolder()->add($filters, 'sf_admin/<?php echo $this->getSingularName() ?>/filters');
    }
<?php endif; ?>
  }

  protected function processSort()
  {
    if ($this->getRequestParameter('sort'))
    {
      $this->getUser()->setAttribute('sort', $this->getRequestParameter('sort'), 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
      $this->getUser()->setAttribute('type', $this->getRequestParameter('type', 'asc'), 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
    }

    if (!$this->getUser()->getAttribute('sort', null, 'sf_admin/<?php echo $this->getSingularName() ?>/sort'))
    {
<?php if ($sort = $this->getParameterValue('list.sort')): ?>
<?php if (is_array($sort)): ?>
      $this->getUser()->setAttribute('sort', '<?php echo $sort[0] ?>', 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
      $this->getUser()->setAttribute('type', '<?php echo $sort[1] ?>', 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
<?php else: ?>
      $this->getUser()->setAttribute('sort', '<?php echo $sort ?>', 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
      $this->getUser()->setAttribute('type', 'asc', 'sf_admin/<?php echo $this->getSingularName() ?>/sort');
<?php endif; ?>
<?php endif; ?>
    }
  }

  protected function addFiltersCriteria($c)
  {
<?php if ($this->getParameterValue('list.filters')): ?>
<?php foreach ($this->getColumns('list.filters') as $column): $type = $column->getCreoleType() ?>
<?php if (($column->isPartial() || $column->isComponent()) && $this->getParameterValue('list.fields.'.$column->getName().'.filter_criteria_disabled')) continue ?>
    if (isset($this->filters['<?php echo $column->getName() ?>_is_empty']))
    {
      $criterion = $c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, '');
      $criterion->addOr($c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, null, Criteria::ISNULL));
      $c->add($criterion);
    }
<?php if ($type == CreoleTypes::DATE || $type == CreoleTypes::TIMESTAMP): ?>
    else if (isset($this->filters['<?php echo $column->getName() ?>']))
    {
      if (isset($this->filters['<?php echo $column->getName() ?>']['from']) && $this->filters['<?php echo $column->getName() ?>']['from'] !== '')
      {
<?php if ($type == CreoleTypes::DATE): ?>
        $criterion = $c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, date('Y-m-d', $this->filters['<?php echo $column->getName() ?>']['from']), Criteria::GREATER_EQUAL);
<?php else: ?>
        $criterion = $c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, $this->filters['<?php echo $column->getName() ?>']['from'], Criteria::GREATER_EQUAL);
<?php endif; ?>
      }
      if (isset($this->filters['<?php echo $column->getName() ?>']['to']) && $this->filters['<?php echo $column->getName() ?>']['to'] !== '')
      {
        if (isset($criterion))
        {
<?php if ($type == CreoleTypes::DATE): ?>
          $criterion->addAnd($c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, date('Y-m-d', $this->filters['<?php echo $column->getName() ?>']['to']), Criteria::LESS_EQUAL));
<?php else: ?>
          $criterion->addAnd($c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, $this->filters['<?php echo $column->getName() ?>']['to'], Criteria::LESS_EQUAL));
<?php endif; ?>
        }
        else
        {
<?php if ($type == CreoleTypes::DATE): ?>
          $criterion = $c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, date('Y-m-d', $this->filters['<?php echo $column->getName() ?>']['to']), Criteria::LESS_EQUAL);
<?php else: ?>
          $criterion = $c->getNewCriterion(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, $this->filters['<?php echo $column->getName() ?>']['to'], Criteria::LESS_EQUAL);
<?php endif; ?>
        }
      }

      if (isset($criterion))
      {
        $c->add($criterion);
      }
    }
<?php else: ?>
    else if (isset($this->filters['<?php echo $column->getName() ?>']) && $this->filters['<?php echo $column->getName() ?>'] !== '')
    {
<?php if ($type == CreoleTypes::CHAR || $type == CreoleTypes::VARCHAR || $type == CreoleTypes::LONGVARCHAR): ?>
      $c->add(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, strtr($this->filters['<?php echo $column->getName() ?>'], '*', '%'), Criteria::LIKE);
<?php else: ?>
      $c->add(<?php echo $this->getPeerClassName() ?>::<?php echo strtoupper($column->getName()) ?>, $this->filters['<?php echo $column->getName() ?>']);
<?php endif; ?>
    }
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
  }

  protected function addSortCriteria($c)
  {
    if ($sort_column = $this->getUser()->getAttribute('sort', null, 'sf_admin/<?php echo $this->getSingularName() ?>/sort'))
    {
      $sort_column = <?php echo $this->getClassName() ?>Peer::translateFieldName($sort_column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
      if ($this->getUser()->getAttribute('type', null, 'sf_admin/<?php echo $this->getSingularName() ?>/sort') == 'asc')
      {
        $c->addAscendingOrderByColumn($sort_column);
      }
      else
      {
        $c->addDescendingOrderByColumn($sort_column);
      }
    }
  }

  protected function getLabels()
  {
    return array(
<?php foreach ($this->getColumnCategories('edit.display') as $category): ?>
<?php foreach ($this->getColumns('edit.display', $category) as $name => $column): ?>
      '<?php echo $this->getSingularName() ?>{<?php echo $column->getName() ?>}' => '<?php $label_name = str_replace("'", "\\'", $this->getParameterValue('edit.fields.'.$column->getName().'.name')); echo $label_name ?><?php if ($label_name): ?>:<?php endif ?>',
<?php endforeach; ?>
<?php endforeach; ?>
    );
  }
}
