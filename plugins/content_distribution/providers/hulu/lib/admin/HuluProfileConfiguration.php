<?php 
/**
 * @package plugins.huluDistribution
 * @subpackage admin
 */
class Form_HuluProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->setDescription('Hulu Distribution Profile');
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'hulu-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$upload = new Zend_File_Transfer_Adapter_Http();
        $files = $upload->getFileInfo();
		
		if(isset($files['aspera_public_key']))
        	$object->asperaPublicKey = $this->getFileContent($files['aspera_public_key']);
        
       	if(isset($files['aspera_private_key']))
            $object->asperaPrivateKey = $this->getFileContent($files['aspera_private_key']);
            
		$additionalCategories = isset($properties['series_additional_categories']) && is_array($properties['itemXpathsToExtend']) ? $properties['series_additional_categories'] : array();
		foreach($additionalCategories as &$val)
		{
			$temp = new Kaltura_Client_Type_String();
			$temp->value = $val;
			$val = $temp;
		}
		$object->seriesAdditionalCategories = $additionalCategories;
		
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
		parent::populateFromObject($object, $add_underscore);
		
		$this->setDefault('aspera_public_key_readonly', $object->asperaPublicKey);
		$this->setDefault('aspera_private_key_readonly', $object->asperaPrivateKey);
		
		$additionalCategories = array();
		foreach($object->seriesAdditionalCategories as $category)
		{
			$additionalCategories[] = $category->value;
		}
		
		$this->setDefault('series_additional_categories', $additionalCategories);
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Hulu Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		
		$this->addElements(array($element));
		
		$this->addElement('select', 'protocol', array(
			'label'			=> 'Protocol:',
			'filters'		=> array('StringTrim'),
			'multiOptions' 		=> array(
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::SFTP=> 'SFTP',
				Kaltura_Client_ContentDistribution_Enum_DistributionProtocol::ASPERA => 'ASPERA',
			),
			'required'		=> true,
		));
		
		$this->addElement('text', 'sftp_host', array(
			'label'			=> 'SFTP Host:',
			'filters'		=> array('StringTrim'),
			'default'		=> 'sftp.hulu.com'
		));
		
		$this->addElement('text', 'aspera_host', array(
			'label'			=> 'Aspera Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'port', array(
			'label'			=> 'Port:',
			'filters'		=> array('StringTrim'),
			'value'			=> '22',
			'required'		=> true,
		));
	
		$this->addElement('text', 'sftp_login', array(
			'label'			=> 'SFTP Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'sftp_pass', array(
			'label'			=> 'SFTP Password:',
			'filters'		=> array('StringTrim'),
		));
		
		
		$this->addElement('text', 'aspera_login', array(
			'label'			=> 'Aspera Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'aspera_pass', array(
			'label'			=> 'Aspera Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'passphrase', array(
            'label'			=> 'Key Passphrase:',
            'filters'		=> array('StringTrim'),
        ));
		
		$this->addElement('file', 'aspera_public_key', array(
			'label'			=> 'Public Key:',
		));

        $this->addElement('textarea', 'aspera_public_key_readonly', array(
            'label'			=> 'Public Key:',
            'readonly'      => true,
        ));
		
		$this->addElement('file', 'aspera_private_key', array(
			'label'			=> 'Private Key:',
		));

        $this->addElement('textarea', 'aspera_private_key_readonly', array(
            'label'			=> 'Private Key:',
            'readonly'      => true,
        ));
		
		$this->addDisplayGroup(
			array('protocol', 'sftp_host', 'port', 'sftp_login', 'sftp_pass', 'aspera_host', 'aspera_login', 'aspera_pass', 'passphrase', 'aspera_public_key','aspera_public_key_readonly','aspera_private_key','aspera_private_key_readonly'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('text', 'series_channel', array(
			'label'			=> 'Series Channel:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('select', 'series_primary_category', array(
			'label'			=> 'Series Primary Category:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array_merge(array('' => ''), $this->getPrimaryCategoryList())
		));
		
		$this->addElement('multiCheckbox', 'series_additional_categories', array(
			'label'			=> 'Series Additional Categories:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> $this->getPrimaryCategoryList()
		));
		
		$this->addElement('text', 'season_number', array(
			'label'			=> 'Season Number:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('text', 'season_synopsis', array(
			'label'			=> 'Season Synopsis:',
			'filters'		=> array('StringTrim')
		));
		
		$this->addElement('text', 'season_tune_in_information', array(
			'label'			=> 'Season Tune In Information:',
			'filters'		=> array('StringTrim')
		));

		$this->addElement('select', 'video_media_type', array(
			'label'			=> 'Video Media Type:',
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> array_merge(array('' => ''), $this->getVideoMediaTypeList())
		));
		
		$this->addDisplayGroup(
			array('series_channel', 'series_primary_category', 'series_additional_categories', 'season_number', 'season_synopsis', 'season_tune_in_information', 'video_media_type'), 
			'default_config_group', 
			array(
				'legend' => 'Default Metadata', 
				'decorators' => array(
					'FormElements', 
					'Fieldset'
				)
			)
		);
		
		$this->addElement('checkbox', 'disable_episode_number_custom_validation', array(
			'label'			=> 'Disable Episode Number Custom Validation:'
		));
		
		$this->addDisplayGroup(
			array('disable_episode_number_custom_validation'), 
			'custom_config_group', 
			array(
				'legend' => '', 
				'decorators' => array(
					'FormElements', 
					'Fieldset'
				)
			)
		);
	}
	
	protected function getPrimaryCategoryList()
	{
		return array(
			'Action and Adventure' => 'Action and Adventure',
			'Animation' => 'Animation',
			'Celebrity and Gossip' => 'Celebrity and Gossip',
			'College Football' => 'College Football',
			'College Sports' => 'College Sports',
			'Comedy' => 'Comedy',
			'Crime and Mystery' => 'Crime and Mystery',
			'Documentary and Biography' => 'Documentary and Biography',
			'Drama' => 'Drama',
			'Extreme Sports' => 'Extreme Sports',
			'Family and Kids' => 'Family and Kids',
			'Gaming' => 'Gaming',
			'Horror and Thriller' => 'Horror and Thriller',
			'House and Home' => 'House and Home',
			'International' => 'International',
			'Lifestyle and Fashion' => 'Lifestyle and Fashion',
			'Live Events and Specials' => 'Live Events and Specials',
			'Mixed Martial Arts/Fighting' => 'Mixed Martial Arts/Fighting',
			'Music' => 'Music',
			'News and Information' => 'News and Information',
			'Outdoor Sports' => 'Outdoor Sports',
			'Political' => 'Political',
			'Reality and Game Show' => 'Reality and Game Show',
			'Sci Fi and Fantasy' => 'Sci Fi and Fantasy',
			'Soap Opera' => 'Soap Opera',
			'Sports and Fitness' => 'Sports and Fitness',
			'Talk and Interview' => 'Talk and Interview',
			'Technology' => 'Technology',
			'Travel and Nature' => 'Travel and Nature',
		);
	}
	
	protected function getVideoMediaTypeList()
	{
		return array(
			'TV' => 'TV',
			'Film' => 'Film',
			'Music Video' => 'Music Video',
			'Web Original' => 'Web Original',
			'Sports' => 'Sports'
		);
	}
}