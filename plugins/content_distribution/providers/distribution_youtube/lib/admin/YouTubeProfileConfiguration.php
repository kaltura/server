<?php 
class Form_YouTubeProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof KalturaYouTubeDistributionProfile)
		{
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
         
			if(isset($files['sftp_public_key']))
			{
				$file = $files['sftp_public_key'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->sftpPublicKey = $content;
				}
			}
			
			if(isset($files['sftp_private_key']))
			{
				$file = $files['sftp_private_key'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->sftpPrivateKey = $content;
				}
			}
		}
		return $object;
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YouTube Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'notification_email', array(
			'label'			=> 'Notification Email:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'sftp_host', array(
			'label'			=> 'SFTP Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'sftp_login', array(
			'label'			=> 'SFTP Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('file', 'sftp_public_key', array(
			'label' => 'SFTP Public Key:'
		));
		
		$this->addElement('file', 'sftp_private_key', array(
			'label' => 'SFTP Private Key:'
		));
	}
}