<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 * @abstract
 */
abstract class Form_SshDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function init()
	{
        $this->addElement('text', 'host', array(
			'label'			=> 'Host:',
			'filters'		=> array('StringTrim'),
		));
		
	    $this->addElement('text', 'port', array(
			'label'			=> 'Port:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('file', 'sshPublicKey', array(
			'label' => 'SSH Public Key:',
		));
		
		$this->addElement('textarea', 'publicKey', array(
			'label' => 'SSH Public Key Data:',
			'rows' => '2',
			'cols' => '50',
			'readonly' => '1'
		));
		
		$this->addElement('file', 'sshPrivateKey', array(
			'label' => 'SSH Private Key:',
		));
		
		$this->addElement('textarea', 'privateKey', array(
			'label' => 'SSH Private Key Data:',
			'rows' => '2',
			'cols' => '50',
			'readonly' => '1'
		));
		
		$this->addElement('text', 'passPhrase', array(
			'label'			=> 'SSH Pass Phrase:',
			'filters'		=> array('StringTrim'),
		));
	}
	
	
	public function getObject($object, $objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
        if ($object instanceof Kaltura_Client_DropFolder_Type_SshDropFolder)
		{
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
         
			if(isset($files['sshPublicKey']))
			{
				$file = $files['sshPublicKey'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->publicKey = $content;
				}
			}
			
			if(isset($files['sshPrivateKey']))
			{
				$file = $files['sshPrivateKey'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->privateKey = $content;
				}
			}
		}
		return $object;
	}		
}