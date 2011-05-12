<?php
class Kaltura_View_Helper_EnumTranslate extends Zend_View_Helper_Abstract
{
	public function enumTranslate($enumName, $value)
	{
		if(!class_exists($enumName))
			return $value;
			
		$oClass = new ReflectionClass($enumName);

		$constants = $oClass->getConstants();
		
		foreach($constants as $constName => $constValue)
			if($constValue == $value)
				return $this->view->translate("$enumName::$constName");
				
		return $value;
	}
}