<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class viewUiconfWidgetsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		myDbHelper::$use_alternative_con = null;
		
		$partnerId = $this->getRequestParameter("partnerId", null);
		$uiConfId = $this->getRequestParameter("uiConfId", null);
		$page = $this->getRequestParameter("page", 1);
		
		if ($partnerId !== null && $partnerId !== "")
		{
			$pageSize = 50;
			$c = new Criteria();
			$c->add(widgetPeer::PARTNER_ID, $partnerId);
			if ($uiConfId)
				$c->add(widgetPeer::UI_CONF_ID, $uiConfId);
			$c->addDescendingOrderByColumn(widgetPeer::CREATED_AT);
			
			$total = widgetPeer::doCount($c);
			$lastPage = ceil($total / $pageSize);
			
			$c->setOffset(($page - 1) * $pageSize);
			$c->setLimit($pageSize);
			
			$widgets = widgetPeer::doSelect($c);
		}
		else
		{
			$total = 0;
			$lastPage = 0;
			$widgets = array();
		}
		
		$this->uiConfId = $uiConfId;
		$this->page = $page;
		$this->lastPage = $lastPage;
		$this->widgets = $widgets;
		$this->partner = PartnerPeer::retrieveByPK($partnerId);;
		$this->partnerId = $partnerId;
	}
}

?>