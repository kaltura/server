<?php
class Kaltura_View_Helper_CssId extends Zend_View_Helper_Abstract
{
	public function cssId()
	{
	    KalturaLog::debug("### ".__LINE__);
		$front = Zend_Controller_Front::getInstance();
		return $front->getRequest()->getControllerName() . '-' . $front->getRequest()->getActionName();
	}
}