<?php
/**
 * @package plugins.playReady
 * @subpackage Admin
 * @abstract
 */
class Form_PlayReadyProfileConfigureExtend_SubForm extends Form_DrmProfileConfigureExtend_SubForm
{
	public function init()
	{
        $this->addElement('text', 'keySeed', array(
			'label'			=> 'Key Seed:',
			'filters'		=> array('StringTrim'),
		));
		
        $this->addElement('button', 'generateKeySeedButton', array(
			'onclick'		=> "generateKeySeed()",
            'label'    => 'Generate Key Seed',
        	'decorators' => array('ViewHelper')
        ));
	}
}