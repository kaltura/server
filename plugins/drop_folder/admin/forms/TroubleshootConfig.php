<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_TroubleshootConfig extends Infra_Form
{
	//		$this->view->tr
	
	public function init()
	{
		$this->addElement('hidden', 'crossLine5', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		 ));
		
		$titleElement = new Zend_Form_Element_Hidden('troubleshootTitle');
		$titleElement->setLabel('Troubleshoot');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);
		
		$status = new Kaltura_Form_Element_EnumSelect('status', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderStatus'));
		$status->setLabel('Last Drop Folder Access Status:');
		$status->setAttrib('readonly', true);
		$status->setAttrib('disabled', 'disabled');
		$this->addElement($status);
		
		$this->addElement('text', 'updatedAt', array(
			'label'			=> 'Time/Date',
			'disabled'		=>true,
			'filters'		=> array('StringTrim'),
		));
			
		$this->addElement('textarea', 'errorDescription', array(
			'label'			=> 'Error Description:',
			'disabled'		=>true,
			'rows'			=> 3,
			'filters'		=> array('StringTrim'),
		));	

		$this->setDecorators(array(
	    	'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmTroubleshootConfig')),
        ));
		
	}

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
				
		$updatedAtElm = $this->getElement('updatedAt');		
		$formattedDate = date($this->getView()->translate('time format'), $updatedAtElm->getValue());
		$updatedAtElm->setValue($formattedDate);
	}
	
 }