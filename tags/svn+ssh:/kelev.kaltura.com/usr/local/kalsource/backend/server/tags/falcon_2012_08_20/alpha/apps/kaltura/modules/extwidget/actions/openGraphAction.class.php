<?php
/**
 * 
 * @package Core
 * @subpackage externalWidgets
 */
class openGraphAction extends sfAction
{
	public function execute()
	{
		$widgetId = $this->getRequestParameter("wid");
		$widget = widgetPeer::retrieveByPK($widgetId);
		if(!$widget)
		{
			KalturaLog::err("Widget id [$widgetId] not found");
			die();
		}
		
		$entry = $widget->getentry();
		$entryId = $widget->getEntryId();
		if(!$entry)
		{
			KalturaLog::err("Entry id [$entryId] not found");
			die();
		}
		
		$uiConf = $widget->getuiConf();
		$uiConfId = $widget->getUiConfId();
		if(!$uiConf)
		{
			KalturaLog::err("UI Conf id [$uiConfId] not found");
			die();
		}
		
		$widgetPath = "/kwidget/wid/$widgetId/entry_id/$entryId/ui_conf/$uiConfId"; 
		
		$this->widget = $widget;
		$this->entry = $entry; 
		$this->uiConf = $uiConf;

		$this->entryThumbUrl = $entry->getThumbnailUrl();
		$this->entryThumbSecureUrl = $entry->getThumbnailUrl(null, 'https');
		$this->widgetUrl = 'http://'.kConf::get('www_host') . $widgetPath;
		$this->widgetSecureUrl = 'https://'.kConf::get('www_host') . $widgetPath;
	}
}
