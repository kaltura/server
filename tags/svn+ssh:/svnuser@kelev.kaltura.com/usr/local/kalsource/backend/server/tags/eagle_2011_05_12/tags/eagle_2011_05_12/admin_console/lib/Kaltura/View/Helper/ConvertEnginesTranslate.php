<?php
class Kaltura_View_Helper_ConvertEnginesTranslate extends Zend_View_Helper_Abstract
{
	public function convertEnginesTranslate($engines)
	{
		$enginesArr = explode(',', $engines);
		$strArr = array();
		foreach($enginesArr as $engine)
			$strArr[] = $this->view->enumTranslate('Kaltura_Client_Enum_ConversionEngineType', $engine);
		
		return join(', ', $strArr);
	}
}