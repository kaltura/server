<?php 
/**
 * @package plugins.pushToNewsDistribution
 * @subpackage admin
 */
class Form_PushToNewsProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	private $metadataProfileFields;
		
	public function init()
	{
		parent::init();
		$this->setDescription('Push To News Distribution Profile');
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'push-to-news-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		/* @var $object Kaltura_Client_FtpDistribution_Type_FtpDistributionProfile */
		$object = parent::getObject($objectType, $properties, $add_underscore, true);
	        $upload = new Zend_File_Transfer_Adapter_Http();
	        $files = $upload->getFileInfo();

		if(isset($files['certificate_key']))
		       	$object->certificateKey = $this->getFileContent($files['certificate_key']);

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

		$this->setDefault('certificate_key_readonly', $object->certificateKey);
		
		//in edit mode of the form  
		$this->getElement('protocol')->setAttrib('disabled',true);

		foreach($fieldConfigArray as $fieldConfig)
		{
			if (!isset($fieldConfig->updateParams[0]) || !isset($fieldConfig->updateParams[0]->value))
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
		$element->setLabel('Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		
		$this->addElements(array($element));
		
		$this->addElement('select', 'protocol', array(
			'label'			=> 'Protocol:',
			'filters'		=> array('StringTrim'),
			'multiOptions' 		=> array(
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::HTTPS => 'HTTPS',
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

		$this->addElement('file', 'certificate_key', array(
			'label'			=> 'Certificate Key:',
		));

		$this->addElement('textarea', 'certificate_key_readonly', array(
	            'label'			=> 'Certificate Key:',
	            'readonly'      => true,
	        ));
		
		$this->addDisplayGroup(
			array('protocol', 'host', 'port', 'base_path', 'username', 'password', 'passphrase', 'certificate_key', 'certificate_key_readonly'),
			'server', 
			array('legend' => 'Server', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addMetadataForm();
		
		$this->addEntryFields();
		$this->addMetadataFields();
	}
	
	protected function addMetadataForm() 
	{
	
		$this->addElement('checkbox', 'enable_id', array(
		'label'			=> 'Id',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_id')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'id_xslt', array(
			'label'			=> 'id',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('id_xslt')->removeDecorator('Label');

		$this->addElement('checkbox', 'enable_publishdat', array(
			'label'			=> 'Publish Date',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_publishdat')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'publishdat_xslt', array(
			'label'			=> 'publishdat',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('publishdat_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_creationat', array(
			'label'			=> 'Creation Date',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_creationat')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'creationat_xslt', array(
			'label'			=> 'creationat',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('creationat_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_titlelanguagedat', array(
		'label'			=> 'Title Language',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_titlelanguagedat')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'titlelanguagedat_xslt', array(
			'label'			=> 'titlelanguagedat',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('titlelanguagedat_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_title', array(
			'label'			=> 'Title',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_title')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'title_xslt', array(
			'label'			=> 'title',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('title_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_mimetype', array(
		'label'			=> 'Mime Type',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_mimetype')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'mimetype_xslt', array(
			'label'			=> 'mimetype',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('mimetype_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_language', array(
		'label'			=> 'Language',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_language')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'language_xslt', array(
			'label'			=> 'language',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('language_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_body', array(
		'label'			=> 'HTML Body',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_body')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'body_xslt', array(
			'label'			=> 'body',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('body_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_author_name', array(
			'label'			=> 'Author Name',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_author_name')->getDecorator('Label')->setOption('placement', 'APPEND');

		$this->addElement('textarea', 'author_name_xslt', array(
			'label'			=> 'author_name',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('author_name_xslt')->removeDecorator('Label');
				
		$this->addElement('checkbox', 'enable_author_email', array(
			'label'			=> 'Author Email',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_author_email')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'author_email_xslt', array(
			'label'			=> 'author_email',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('author_email_xslt')->removeDecorator('Label');
				
		$this->addElement('checkbox', 'enable_rightsinfo_copyrightholder', array(
			'label'			=> 'Rightsinfo - copyrightholder',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_rightsinfo_copyrightholder')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'rightsinfo_copyrightholder_xslt', array(
			'label'			=> 'rightsinfo_copyrightholder',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('rightsinfo_copyrightholder_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_rightsinfo_name', array(
			'label'			=> 'Rightsinfo - name',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_rightsinfo_name')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'rightsinfo_name_xslt', array(
			'label'			=> 'rightsinfo_name',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('rightsinfo_name_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_rightsinfo_copyrightnotice', array(
			'label'			=> 'Rightsinfo - copyright notice',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_rightsinfo_copyrightnotice')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'rightsinfo_copyrightnotice_xslt', array(
			'label'			=> 'rightsinfo_copyrightnotice',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('rightsinfo_copyrightnotice_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_productcode', array(
		'label'			=> 'Product Code',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_productcode')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'productcode_xslt', array(
			'label'			=> 'productcode',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('productcode_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_attribution', array(
		'label'			=> 'Attribution',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_attribution')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'attribution_xslt', array(
			'label'			=> 'attribution',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('attribution_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_metadata_organizations', array(
		'label'			=> 'Metadata Organizations',
		'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_metadata_organizations')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'metadata_organizations_xslt', array(
			'label'			=> 'metadata_organizations',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('metadata_organizations_xslt')->removeDecorator('Label');
		
		$this->addElement('checkbox', 'enable_metadata_subjects', array(
			'label'			=> 'Metadata Subjects',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('enable_metadata_subjects')->getDecorator('Label')->setOption('placement', 'APPEND');
		
		$this->addElement('textarea', 'metadata_subjects_xslt', array(
			'label'			=> 'metadata_subjects',
			'filters'		=> array('StringTrim'),
		));
		$this->getElement('metadata_subjects_xslt')->removeDecorator('Label');
		
		$this->addDisplayGroup(
			array('enable_id','id_xslt','enable_publishdat','publishdat_xslt','enable_creationat','creationat_xslt','enable_titlelanguagedat','titlelanguagedat_xslt','enable_title','title_xslt','enable_mimetype','mimetype_xslt','enable_language','language_xslt','enable_body','body_xslt','enable_author_name','author_name_xslt','enable_author_email','author_email_xslt','enable_rightsinfo_copyrightholder','rightsinfo_copyrightholder_xslt','enable_rightsinfo_name','rightsinfo_name_xslt','enable_rightsinfo_copyrightnotice','rightsinfo_copyrightnotice_xslt','enable_productcode','productcode_xslt','enable_attribution','attribution_xslt','enable_metadata_organizations','metadata_organizations_xslt','enable_metadata_subjects','metadata_subjects_xslt'),
			'file_names', 
			array('legend' => 'Xslt values', 'decorators' => array('FormElements', 'Fieldset'))
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
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $optionalFlavorParamsIds = array(), array $requiredFlavorParamsIds = array())
	{
		//do nothing
	}

}
