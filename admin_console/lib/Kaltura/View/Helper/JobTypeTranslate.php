<?php
class Kaltura_View_Helper_JobTypeTranslate extends Zend_View_Helper_Abstract
{
	public function jobTypeTranslate($jobType, $jobSubType = null)
	{
		if($jobType == KalturaBatchJobType::CONVERT && $jobSubType)
			return $this->view->enumTranslate('KalturaConversionEngineType', $jobSubType);
			
		return $this->view->enumTranslate('KalturaBatchJobType', $jobType);
	}
}