<?php 
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_DropFolderFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'none' => 'None', 
			'partnerIdEqual' => 'Publisher ID',
			'idEqual' => 'Drop Folder ID',
			'nameLike' => 'Drop Folder Name Like'
		));
	}
}