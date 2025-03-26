<?php

class GenericAppAdminAction extends KalturaApplicationPlugin
{
	const LABEL = "Generic App";
	public function __construct()
	{
		$this->rootLabel = KalturaApplicationLinksPlugin::ROOT_LABEL;
		$this->label = self::LABEL;
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$ks = Infra_ClientHelper::getKs();
		$urlsCsv = Zend_Registry::get('config')->applicationLinks->GenericApp->loginUrl;
		$urls = explode(',', $urlsCsv);

		$modifiedUrls = array();
		foreach ($urls as $url) {
			$loginUrl = $url . '?ks=' . $ks;
			$modifiedUrls[] = [$loginUrl,$url];
		}
		$action->view->urls = $modifiedUrls;
	}

	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
}
