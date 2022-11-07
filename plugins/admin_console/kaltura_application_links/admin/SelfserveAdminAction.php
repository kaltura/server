<?php

class SelfserveAdminAction extends KalturaApplicationPlugin
{
    public function __construct()
    {
        $this->rootLabel = "External apps";
        $this->label = "Selfserve";
    }

    public function doAction(Zend_Controller_Action $action)
    {
        $ks = Infra_ClientHelper::getKs();
        $appUrl =  Zend_Registry::get('config')->applicationLinks->selfserve->appUrl;
        $loginUrl = Zend_Registry::get('config')->applicationLinks->selfserve->loginUrl;

        //load the application page
        $action->view->appUrl = $appUrl;
        $action->view->loginUrl =  $loginUrl.$ks;;
    }

    /**
     * @return string - absolute file path of the phtml template
     */
    public function getTemplatePath()
    {
        return realpath(dirname(__FILE__));
    }

}
