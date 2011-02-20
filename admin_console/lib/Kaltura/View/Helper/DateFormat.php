<?php
class Kaltura_View_Helper_DateFormat extends Zend_View_Helper_Abstract
{
	public function dateFormat($timestamp, $format = Zend_Date::ISO_8601)
	{
		$d = (new Zend_Date($timestamp)); 
		return $d->toString($format);
	}
}