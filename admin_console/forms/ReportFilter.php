<?php 
/**
 * @package Admin
 * @subpackage Reports
 */
class Form_ReportFilter extends Form_PartnerFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmReportFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None',
			'by-report-id' => 'Report ID',  
			'by-partner-id' => 'Publisher ID',
			'by-partner-name' => 'Publisher Name',
		));
	}
}