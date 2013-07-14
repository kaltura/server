<?php 
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class Form_VirusScanFilter extends Form_PartnerIdFilter
{
	public function init()
	{
			parent::init();
		$this->setAttrib('id', 'frmVirusScanFilter');
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
				'none' => 'None', 
				'partnerIdEqual' => 'Publisher ID',
				'idIn' => 'Profile ID',
				'nameLike' => 'Profile Name',
		));
		
	}
}