<?php 
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_MediaRepurposingFilter extends Form_PartnerIdFilter
{

	public $partnerId;

	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None', 
			'partnerIdEqual' => 'Publisher ID',
			'idEqual' => 'Media Repurposing ID'
		));
	}


}