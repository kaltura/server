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
		KalturaLog::debug('Moshe ' . $urlsCsv);
		$urls = explode(',', $urlsCsv);
		KalturaLog::debug('Moshe ' . print_r($urls, true));

		$modifiedUrls = array();
		foreach ($urls as $url) {
			KalturaLog::debug('Moshe ' . $url);
			$loginUrl = $url . '?ks=' . $ks;
			KalturaLog::debug('Moshe ' . $loginUrl);
			$modifiedUrls[] = [$loginUrl,$url];
		}
		KalturaLog::debug('Moshe ' . print_r($modifiedUrls, true));
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
