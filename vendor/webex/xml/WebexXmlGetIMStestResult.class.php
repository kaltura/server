<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlQtiQti_result_reportType.class.php');

class WebexXmlGetIMStestResult extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlQtiQti_result_reportType
	 */
	protected $qti_result_report;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qti_result_report':
				return 'WebexXmlQtiQti_result_reportType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlQtiQti_result_reportType $qti_result_report
	 */
	public function getQti_result_report()
	{
		return $this->qti_result_report;
	}
	
}

