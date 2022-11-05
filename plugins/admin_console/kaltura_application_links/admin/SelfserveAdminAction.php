<?php

class SelfserveAdminAction extends KalturaApplicationPlugin
{
    public function __construct()
    {
        $this->rootLabel = "External Links";
        $this->label = "Selfserve";
    }

    public function doAction(Zend_Controller_Action $action)
    {

        //try login using KS
        $ks = Infra_ClientHelper::getKs();
        //$appUrl = kConf::getArrayValue("appUrl" , "selfserve", "application_links");
        $appUrl =  Zend_Registry::get('config')->applicationLinks->selfserve->appUrl;
        $loginUrl = Zend_Registry::get('config')->applicationLinks->selfserve->loginUrl;
        $url = $loginUrl.$ks;

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