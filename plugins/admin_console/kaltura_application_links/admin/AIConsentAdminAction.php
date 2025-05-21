<?php

class AIConsentAdminAction extends KalturaApplicationPlugin
{
	const LABEL = "AI Consent admin";
	public function __construct()
	{
		$this->rootLabel = KalturaApplicationLinksPlugin::ROOT_LABEL;
		$this->label = self::LABEL;
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$ks = Infra_ClientHelper::getKs();
		$loginUrl = Zend_Registry::get('config')->applicationLinks->AIConsent->loginUrl;
		$action->view->loginUrl = $loginUrl . $ks;
	}

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
}
