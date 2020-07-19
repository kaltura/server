<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachRequestsExportAction extends KalturaApplicationPlugin
{
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$filterType = $this->_getParam('filter_type');
		$filterInput = $this->_getParam('filter_input');
		$status = $this->_getParam('filter_status');
		$dueDate = $this->_getParam('due_date');

		$client = Infra_ClientHelper::getClient();
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($client);
		$exportUrl = $reachPluginClient->entryVendorTask->getServeUrl($filterType, $filterInput, $status, $dueDate);
		$action->view->exportUrl = $exportUrl;
	}
}
