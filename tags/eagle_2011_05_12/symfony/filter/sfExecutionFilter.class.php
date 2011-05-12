<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfExecutionFilter is the last filter registered for each filter chain. This
 * filter does all action and view execution.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfExecutionFilter.class.php 3244 2007-01-12 14:46:11Z fabien $
 */
class sfExecutionFilter extends sfFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain The filter chain
   *
   * @throws <b>sfInitializeException</b> If an error occurs during view initialization.
   * @throws <b>sfViewException</b>       If an error occurs while executing the view.
   */
  public function execute($filterChain)
  {
    // get the context and controller
    $context    = $this->getContext();
    $controller = $context->getController();

    // get the current action instance
    $actionEntry    = $controller->getActionStack()->getLastEntry();
    $actionInstance = $actionEntry->getActionInstance();

    // get the current action information
    $moduleName = $context->getModuleName();
    $actionName = $context->getActionName();

    // get the request method
    $method = $context->getRequest()->getMethod();

    $viewName = null;

    if (sfConfig::get('sf_cache'))
    {
      $uri = sfRouting::getInstance()->getCurrentInternalUri();
      if (null !== $context->getResponse()->getParameter($uri.'_action', null, 'symfony/cache'))
      {
        // action in cache, so go to the view
        $viewName = sfView::SUCCESS;
      }
    }

    if (!$viewName)
    {
      if (($actionInstance->getRequestMethods() & $method) != $method)
      {
        // this action will skip validation/execution for this method
        // get the default view
        $viewName = $actionInstance->getDefaultView();
      }
      else
      {
        // set default validated status
        $validated = true;

        // get the current action validation configuration
        $validationConfig = $moduleName.'/'.sfConfig::get('sf_app_module_validate_dir_name').'/'.$actionName.'.yml';

        // load validation configuration
        // do NOT use require_once
        if (null !== $validateFile = sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$validationConfig, true))
        {
          // create validator manager
          $validatorManager = new sfValidatorManager();
          $validatorManager->initialize($context);

          require($validateFile);

          // process validators
          $validated = $validatorManager->execute();
        }

        // process manual validation
        $validateToRun = 'validate'.ucfirst($actionName);
        $manualValidated = method_exists($actionInstance, $validateToRun) ? $actionInstance->$validateToRun() : $actionInstance->validate();

        // action is validated if:
        // - all validation methods (manual and automatic) return true
        // - or automatic validation returns false but errors have been 'removed' by manual validation
        $validated = ($manualValidated && $validated) || ($manualValidated && !$validated && !$context->getRequest()->hasErrors());

        // register fill-in filter
        if (null !== ($parameters = $context->getRequest()->getAttribute('fillin', null, 'symfony/filter')))
        {
          $this->registerFillInFilter($filterChain, $parameters);
        }

        if ($validated)
        {
          if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
          {
            $timer = sfTimerManager::getTimer(sprintf('Action "%s/%s"', $moduleName, $actionName));
          }

          // execute the action
          $actionInstance->preExecute();
          $viewName = $actionInstance->execute();
          if ($viewName == '')
          {
            $viewName = sfView::SUCCESS;
          }
          $actionInstance->postExecute();

          if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
          {
            $timer->addTime();
          }
        }
        else
        {
          if (sfConfig::get('sf_logging_enabled'))
          {
            $this->context->getLogger()->info('{sfFilter} action validation failed');
          }

          // validation failed
          $handleErrorToRun = 'handleError'.ucfirst($actionName);
          $viewName = method_exists($actionInstance, $handleErrorToRun) ? $actionInstance->$handleErrorToRun() : $actionInstance->handleError();
          if ($viewName == '')
          {
            $viewName = sfView::ERROR;
          }
        }
      }
    }

    if ($viewName == sfView::HEADER_ONLY)
    {
      $context->getResponse()->setHeaderOnly(true);

      // execute next filter
      $filterChain->execute();
    }
    else if ($viewName != sfView::NONE)
    {
      if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
      {
        $timer = sfTimerManager::getTimer(sprintf('View "%s" for "%s/%s"', $viewName, $moduleName, $actionName));
      }

      // get the view instance
      $viewInstance = $controller->getView($moduleName, $actionName, $viewName);

      $viewInstance->initialize($context, $moduleName, $actionName, $viewName);

      $viewInstance->execute();

      // render the view and if data is returned, stick it in the
      // action entry which was retrieved from the execution chain
      $viewData = $viewInstance->render();

      if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
      {
        $timer->addTime();
      }

      if ($controller->getRenderMode() == sfView::RENDER_VAR)
      {
        $actionEntry->setPresentation($viewData);
      }
      else
      {
        // execute next filter
        $filterChain->execute();
      }
    }
  }

  /**
   * Registers the fill in filter in the filter chain.
   *
   * @param sfFilterChain A sfFilterChain implementation instance
   * @param array An array of parameters to pass to the fill in filter.
   */
  protected function registerFillInFilter($filterChain, $parameters)
  {
    // automatically register the fill in filter if it is not already loaded in the chain
    if (isset($parameters['enabled']) && $parameters['enabled'] && !$filterChain->hasFilter('sfFillInFormFilter'))
    {
      // register the fill in form filter
      $fillInFormFilter = new sfFillInFormFilter();
      $fillInFormFilter->initialize($this->context, isset($parameters['param']) ? $parameters['param'] : array());
      $filterChain->register($fillInFormFilter);
    }
  }
}
