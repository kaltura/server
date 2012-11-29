<?php
/**
 * @package KMC
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function indexAction()
    {
        if (!Infra_AclHelper::isAllowed('index', 'index'))
        	$this->_helper->redirector('user', 'login');
    }
}