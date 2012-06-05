<?php
/**
 * @package Var
 */
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if (Infra_AclHelper::isAllowed('partner', 'list'))
        	$this->_helper->redirector('list', 'partner');
    }


}

