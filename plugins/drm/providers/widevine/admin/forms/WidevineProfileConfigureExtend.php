<?php
/**
 * @package plugins.widevine
 * @subpackage Admin
 * @abstract
 */
class Form_WidevineProfileConfigureExtend_SubForm extends Form_DrmProfileConfigureExtend_SubForm
{
	public function init()
	{
        $this->addElement('text', 'regServerHost', array(
			'label'			=> 'Registration Host:',
			'filters'		=> array('StringTrim'),
		));
		
	    $this->addElement('text', 'owner', array(
			'label'			=> 'Owner:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'portal', array(
			'label'			=> 'Portal:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'key', array(
			'label'			=> 'Key:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'iv', array(
			'label'			=> 'IV:',
			'filters'		=> array('StringTrim'),
		));
						
		$this->addElement('text', 'maxGop', array(
			'label'			=> 'Max GOP:',
			'filters'		=> array('StringTrim'),
		));
	}
}