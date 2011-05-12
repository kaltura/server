<?php 
class Form_SystemHelper extends Zend_Form
{
	public function init()
	{
		$this->addElement('radio', 'Algorithm', array(
			'label'	=> 'Algorithm',
			'required' => true,
			'filters'		=> array('StringTrim'),
			'multiOptions' => array(
				'wiki_decode' => 'Wiki Decode',
				'wiki_decode_no_serialize' => 'Wiki Decode (No unserialize)',
				'base64_encode' => 'Base64 Encode',
				'base64_decode' => 'Base64 Decode',
				'base64_3des_encode' => 'Base64 3des Encode key',
				'base64_3des_decode' => 'Base64 3des Decode ',
				'ks' => 'KS',
				'kwid' =>	'kwid (wiki) secret:',
				'ip' =>  'ip to country'				
			),
			'separator' => '<br>'
		));
		
		// Add 
		$this->addElement('text', 'des_key', array(
			'label'		=> 'Base64 3des Encode key:',
			'filters'		=> array('StringTrim'),
		));
		
		 
		$this->addElement('text', 'secret', array(
			'label'			=> 'kwid (wiki) secret:',
			'filters'		=> array('StringTrim'),
		));

		
		$this->addElement('textarea', 'StringToManipulate', array(
			'label'			=> 'String to manipulate:',
			'cols'			=> 48,
			'rows'			=> 3,
			'filters'		=> array('StringTrim'),
		
		));
		
		// Add the submit button
		$this->addElement('button', 'submit', array(
			'type' => 'submit',
			'label'		=> 'Submit',
		));
	}
}