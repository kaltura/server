<?php 
/**
 * @package plugins.ftpDistribution
 * @subpackage admin
 */
class Form_FtpProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	private $metadataProfileFields;
		
	public function init()
	{
		parent::init();
		$this->setDescription('FTP Distribution Profile');
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'ftp-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		/* @var $object Kaltura_Client_FtpDistribution_Type_FtpDistributionProfile */
		$object = parent::getObject($objectType, $properties, $add_underscore, true);
        $upload = new Zend_File_Transfer_Adapter_Http();
        $files = $upload->getFileInfo();

        if(isset($files['sftp_public_key']))
        	$object->sftpPublicKey = $this->getFileContent($files['sftp_public_key']);

        if(isset($files['sftp_private_key']))
            $object->sftpPrivateKey = $this->getFileContent($files['sftp_private_key']);

        if(isset($files['aspera_public_key']))
        	$object->asperaPublicKey = $this->getFileContent($files['aspera_public_key']);
        
       	if(isset($files['aspera_private_key']))
            $object->asperaPrivateKey = $this->getFileContent($files['aspera_private_key']);
            
		$updateRequiredEntryFields = array();
		$updateRequiredMetadataXpaths = array();
		
		$entryFields = array_keys($this->getEntryFields());
		$metadataXpaths = array_keys($this->getMetadataFields());
		$fieldConfigArray = $object->fieldConfigArray;
		foreach($properties as $property => $value)
		{
			if(!$value)
				continue;
			
			$updateField = null;		
			$matches = null;
			if(preg_match('/update_required_entry_fields_(\d+)$/', $property, $matches))
			{
				$index = $matches[1];
				if(isset($entryFields[$index])) 
					$updateField = $entryFields[$index];
			}
		
			if(preg_match('/update_required_metadata_xpaths_(\d+)$/', $property, $matches))
			{
				$index = $matches[1];
				if(isset($metadataXpaths[$index])) 
					$updateField = $metadataXpaths[$index];
			}
			
			if ($updateField) 
			{
				$fieldConfig = new Kaltura_Client_ContentDistribution_Type_DistributionFieldConfig();
				$fieldConfig->fieldName = md5($updateField); // needs to have a value for the field to get saved
				$fieldConfig->updateOnChange = true;
				$string = new Kaltura_Client_Type_String();
				$string->value = $updateField;
				$fieldConfig->updateParams = array($string);
				$fieldConfigArray[] = $fieldConfig;
			}
		}
		
		$object->fieldConfigArray = $fieldConfigArray;
		return $object;
	}
	
	private function getFileContent(array $file){
		if ($file['error'] === UPLOAD_ERR_OK){
               return file_get_contents($file['tmp_name']);			
		}
        return null;
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
        /* @var Kaltura_Client_FtpDistribution_Type_FtpDistributionProfile $object */
		parent::populateFromObject($object, $add_underscore);
		$this->addItemXpathsToExtend($object->itemXpathsToExtend);
		
		$entryFields = array_keys($this->getEntryFields());
		$metadataXpaths = array_keys($this->getMetadataFields());
		
		$fieldConfigArray = $object->fieldConfigArray;

        $this->setDefault('sftp_public_key_readonly', $object->sftpPublicKey);
		$this->setDefault('sftp_private_key_readonly', $object->sftpPrivateKey);
		$this->setDefault('aspera_public_key_readonly', $object->asperaPublicKey);
		$this->setDefault('aspera_private_key_readonly', $object->asperaPrivateKey);
		
		//in edit mode of the form  
		$this->getElement('protocol')->setAttrib('disabled',true);

		foreach($fieldConfigArray as $fieldConfig)
		{
			if (!isset($fieldConfig->updateParams[0]) && isset($fieldConfig->updateParams[0]->value))
				continue;
				
			$field = $fieldConfig->updateParams[0]->value;
			$index = array_search($field, $entryFields);
			if($index !== false)
				$this->setDefault("update_required_entry_fields_{$index}", true);
			
			$index = array_search($field, $metadataXpaths);
			if($index !== false)
				$this->setDefault("update_required_metadata_xpaths_{$index}", true);
		}
	}

	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('FTP/SFTP/ASPERA Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		
		$this->addElements(array($element));
		
		$this->addElement('select', 'protocol', array(
			'label'			=> 'Protocol:',
			'filters'		=> array('StringTrim'),
			'multiOptions' 		=> array(
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::FTP => 'FTP',
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::SFTP => 'SFTP',
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::ASPERA => 'ASPERA',
			),
//			'required'		=> true,
		));
		
		$this->addElement('text', 'host', array(
			'label'			=> 'Host:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'port', array(
			'label'			=> 'Port:',
			'filters'		=> array('StringTrim'),
			'value'			=> '21',
			'required'		=> true,
		));
		
		$this->addElement('text', 'base_path', array(
			'label'			=> 'Base Path:',
			'filters'		=> array('StringTrim'),
			'value'			=> '/',
			'required'		=> true,
		));
	
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));

        $this->addElement('text', 'passphrase', array(
            'label'			=> 'Key Passphrase:',
            'filters'		=> array('StringTrim'),
        ));

		$this->addElement('file', 'sftp_public_key', array(
			'label'			=> 'Sftp Public Key:',
		));

        $this->addElement('textarea', 'sftp_public_key_readonly', array(
            'label'			=> 'Sftp Public Key:',
            'readonly'      => true,
        ));
		
		$this->addElement('file', 'sftp_private_key', array(
			'label'			=> 'Sftp Private Key:',
		));

        $this->addElement('textarea', 'sftp_private_key_readonly', array(
            'label'			=> 'Sftp Private Key:',
            'readonly'      => true,
        ));
        
        $this->addElement('file', 'aspera_public_key', array(
			'label'			=> 'Aspera Public Key:',
		));

        $this->addElement('textarea', 'aspera_public_key_readonly', array(
            'label'			=> 'Aspera Public Key:',
            'readonly'      => true,
        ));
		
		$this->addElement('file', 'aspera_private_key', array(
			'label'			=> 'Aspera Private Key:',
		));

        $this->addElement('textarea', 'aspera_private_key_readonly', array(
            'label'			=> 'Aspera Private Key:',
            'readonly'      => true,
        ));
		
		$this->addDisplayGroup(
			array('protocol', 'host', 'port', 'base_path', 'username', 'password', 'passphrase', 'sftp_public_key', 'sftp_public_key_readonly', 'sftp_private_key', 'sftp_private_key_readonly', 'aspera_public_key', 'aspera_public_key_readonly', 'aspera_private_key', 'aspera_private_key_readonly'),
			'server', 
			array('legend' => 'Server', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addMetadataForm();
		
		$this->addEntryFields();
		$this->addMetadataFields();
	}
	
	protected function addMetadataForm() 
	{
		// custom metadata xslt
		$this->addElement('checkbox', 'disable_metadata', array(
			'label'			=> 'Disable Metadata',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('disable_metadata')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('checkbox', 'send_metadata_after_assets', array(
			'label'			=> 'Send metadata after assets',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('send_metadata_after_assets')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('checkbox', 'enable_metadata_xslt', array(
			'label'			=> 'Custom Metadata Xslt',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_metadata_xslt')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'metadata_xslt', array(
			'label'			=> '',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('metadata_xslt')->removeDecorator('Label');
		
		$this->addDisplayGroup(
			array('disable_metadata', 'send_metadata_after_assets', 'enable_metadata_xslt', 'metadata_xslt'), 
			'metadata', 
			array('legend' => 'Metadata', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		// metadata file name
		$this->addElement('checkbox', 'enable_metadata_filename', array(
			'label'			=> 'Custom Metadata Filename:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_metadata_filename')->getDecorator('Label')->setOption('placement', 'APPEND');
				
		$this->addElement('textarea', 'metadata_filename_xslt', array(
			'label'			=> 'Custom Metadata Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('metadata_filename_xslt')->removeDecorator('Label');
		
		// flavor asset file names
		$this->addElement('checkbox', 'enable_flavor_asset_filename', array(
			'label'			=> 'Custom Flavor Asset Filename:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_flavor_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'flavor_asset_filename_xslt', array(
			'label'			=> 'Flavor Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('flavor_asset_filename_xslt')->removeDecorator('Label');
		
		// thumbnail asset file names
		$this->addElement('checkbox', 'enable_thumbnail_asset_filename', array(
			'label'			=> 'Custom Thumbnail Asset Filename',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_thumbnail_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'thumbnail_asset_filename_xslt', array(
			'label'			=> 'Thumbnail Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('thumbnail_asset_filename_xslt')->removeDecorator('Label');

		// asset file names
		$this->addElement('checkbox', 'enable_asset_filename', array(
			'label'			=> 'Custom Asset Filename',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_asset_filename')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'asset_filename_xslt', array(
			'label'			=> 'Asset Filename Xslt:',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('asset_filename_xslt')->removeDecorator('Label');
		
		$this->addDisplayGroup(
			array('enable_metadata_filename', 'metadata_filename_xslt', 'enable_flavor_asset_filename', 'flavor_asset_filename_xslt', 'enable_thumbnail_asset_filename', 'thumbnail_asset_filename_xslt', 'enable_asset_filename', 'asset_filename_xslt'),
			'file_names', 
			array('legend' => 'File Names', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
	
	protected function addEntryFields()
	{
		$index = 0;
		$elementNames = array();
		foreach($this->getEntryFields() as $field => $fieldName)
		{
			$elementName = "update_required_entry_fields_{$index}";
			$this->addElement('checkbox', $elementName, array(
				'label'	  => $fieldName,
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
			));
			$elementNames[] = $elementName;
			$index++;
		}

        if (count($elementNames))
        {
            $this->addDisplayGroup(
                $elementNames,
                'entry_fields_trigger_update',
                array('legend' => 'Entry Fields that Trigger Update', 'decorators' => array('FormElements', 'Fieldset'))
            );
        }
	}

	protected function addMetadataFields()
	{
		$index = 0;
		$elementNames = array();
		foreach($this->getMetadataFields() as $xPath => $fieldName)
		{
			$elementName = "update_required_metadata_xpaths_{$index}";
			$this->addElement('checkbox', $elementName, array(
				'label'	  => $fieldName,
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
			));
			$elementNames[] = $elementName;
			$index++;
		}

        if (count($elementNames))
        {
            $this->addDisplayGroup(
                $elementNames,
                'metadata_fields_trigger_update',
                array('legend' => 'Metadata Fields that Trigger Update', 'decorators' => array('FormElements', 'Fieldset'))
            );
        }
	}
	
	protected function getMetadataFields()
	{
		if(is_array($this->metadataProfileFields))
			return $this->metadataProfileFields;
			
		$this->metadataProfileFields = array();
		$client = Infra_ClientHelper::getClient();
		$metadataPlugin = Kaltura_Client_Metadata_Plugin::get($client);
		
		Infra_ClientHelper::impersonate($this->partnerId);
		
		try
		{
			$metadataProfileList = $metadataPlugin->metadataProfile->listAction();
			if($metadataProfileList->totalCount)
			{
				$client->startMultiRequest();
				foreach($metadataProfileList->objects as $metadataProfile)
				{
					$metadataFieldList = $metadataPlugin->metadataProfile->listFields($metadataProfile->id);
				}
				$results = $client->doMultiRequest();
				foreach($results as $metadataFieldList)
				{
					foreach($metadataFieldList->objects as $metadataField)
						$this->metadataProfileFields[$metadataField->xPath] = $metadataField->label;
				}
			}
		}
		catch (Exception $e)
		{
			Infra_ClientHelper::unimpersonate();
			throw $e;
		}
		
		Infra_ClientHelper::unimpersonate();

		return $this->metadataProfileFields;
	}

	protected function getEntryFields()
	{
		return array(
            'entry.NAME' => 'entry.NAME',
            'entry.TAGS' => 'entry.TAGS',
            'entry.DESCRIPTION' => 'entry.DESCRIPTION',
            'entry.CATEGORIES' => 'entry.CATEGORIES',
            'entry.START_DATE' => 'entry.START_DATE',
            'entry.END_DATE' => 'entry.END_DATE',
		);
	}
}