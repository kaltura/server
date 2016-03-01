<?php 
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage admin
 */
class Form_ComcastMrssProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		$cPlatformXml = $this->getValue('cplatform_xml');
		$doc = new DOMDocument();
		$doc->loadXML($cPlatformXml);
		$itemsNode = $doc->getElementsByTagName('items')->item(0);
		$cPlatformArray = array();
		if ($itemsNode)
		{
			$itemNodes = $itemsNode->getElementsByTagName('item');
			foreach($itemNodes as $itemNode)
			{
				$keyNode = $itemNode->getElementsByTagName('key')->item(0);
				$valueNode = $itemNode->getElementsByTagName('value')->item(0);
				$keyVal = new Kaltura_Client_Type_KeyValue();
				$keyVal->key = $keyNode->nodeValue;
				$filter = new Zend_Filter_Alnum(true);
				$keyVal->value = $filter->filter($valueNode->nodeValue);
				$cPlatformArray[] = $keyVal;
			}
		}
		$object->cPlatformTvSeries = $cPlatformArray;
		$object->cPlatformTvSeriesField = $this->getValue('c_platform_tv_series_field'); // because parent::getObject doesn't include empty fields 
		$object->shouldIncludeCaptions = $this->getValue('should_include_captions');
		$object->shouldIncludeCuePoints = $this->getValue('should_include_cue_points');
		$object->shouldAddThumbExtension = $this->getValue('should_add_thumb_extension');
		$object->feedLink = $this->getValue('feed_link'); // because parent::getObject doesn't include empty fields 
			
		return $object;
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		$xml = "<items>\r\n";
		foreach($object->cPlatformTvSeries as $keyVal)
		{
			$xml .= "	<item>\r\n";
			$xml .= "		<key>".htmlentities($keyVal->key)."</key>\r\n";
			$xml .= "		<value>".htmlentities($keyVal->value)."</value>\r\n";
			$xml .= "	</item>\r\n";
		}
		$xml .= "</items>";
		$this->setDefault('cplatform_xml', $xml);
	}
	
	public function saveProviderAdditionalObjects(Kaltura_Client_ContentDistribution_Type_DistributionProfile $distributionProfile)
	{
		if ($distributionProfile instanceof Kaltura_Client_ComcastMrssDistribution_Type_ComcastMrssDistributionProfile)
		{
			if ($this->getValue('c_platform_tv_series_field'))
			{
				$profileIdFieldName = explode(':', $this->getValue('c_platform_tv_series_field'));
				$profileId = $profileIdFieldName[0];
				$fieldName = $profileIdFieldName[1];
				$client = Infra_ClientHelper::getClient();
				$metadataPlugin = Kaltura_Client_Metadata_Plugin::get($client);
				$profile = $metadataPlugin->metadataProfile->get($profileId);
				$doc = new DOMDocument();
				$doc->loadXML($profile->xsd);
				$xpath = new DOMXPath($doc);
				$xpath->registerNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
				$element = $xpath->query('//xsd:element[@name="'.$fieldName.'"]')->item(0);
				if ($element)
				{
					$restrictionNode = $xpath->query('xsd:simpleType/xsd:restriction[@base="listType"]', $element)->item(0);
					if ($restrictionNode)
					{
						// get existing values
						$existingValues = array();
						$enumerationNodes = $xpath->query('xsd:enumeration', $restrictionNode);
						foreach($enumerationNodes as $enum)
							$existingValues[] = $enum->getAttribute('value');
						sort($existingValues);
						
						// get new values
						$newValues = array();
						foreach($distributionProfile->cPlatformTvSeries as $keyVal)
							$newValues[] = $keyVal->value;
						sort($newValues);
						
						// compare it
						if ($existingValues !== $newValues)
						{
							KalturaLog::info('Updating metadata profile ['.$profileId.'] field ['.$fieldName.'] with new list of values');
							
							// clear existing values
							foreach($enumerationNodes as $enum)
								$existingValues[] = $restrictionNode->removeChild($enum);
								
							// add new values
							foreach($newValues as $val)
							{
								$enumNode = $doc->createElement('xsd:enumeration');
								$enumNode->setAttribute('value', htmlentities($val));
								$restrictionNode->appendChild($enumNode);
							}
							$metadataPlugin->metadataProfile->update($profileId, new Kaltura_Client_Metadata_Type_MetadataProfile(), $doc->saveXML());
						}
						else
						{
							KalturaLog::info('No fields update is required for metadata ['.$profileId.'] field ['.$fieldName.']');
						}
					}
				}
			}
		}
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Comcast MRSS Provider Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_title');
		$element->setLabel('Feed title:');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_StringLength(0, 38));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_link');
		$element->setLabel('Feed link:');
		$element->addValidator(new Zend_Validate_StringLength(0, 80));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_description');
		$element->setLabel('Feed description:');
		$element->setRequired(true);
		$element->addValidator(new Zend_Validate_StringLength(0, 128));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_last_build_date');
		$element->setLabel('Feed last build date:');
		$element->setRequired(true);
		$date = new DateTime('now', new DateTimeZone('UTC'));
		$element->setValue(str_replace('+0000', 'Z', $date->format(DateTime::ISO8601))); // comcast used Z for UTC timezone in their example (2008-04-11T12:30:00Z) 
		$this->addElement($element);
		
		$this->addElement('select', 'c_platform_tv_series_field', array(
			'label' => 'cPlatform TV Series Field:',
		));
		
		$this->addElement('checkbox', 'should_include_captions', array(
			'label' => 'Include Entry Closed Captions',
		));
		
		$this->addElement('checkbox', 'should_include_cue_points', array(
			'label' => 'Include Entry Ad Cue Points',
		));

		$this->addElement('checkbox', 'should_add_thumb_extension', array(
 			'label' => 'Include Thumbnail Extension',
 		));
		
		$this->addElement('textarea', 'cplatform_xml', array(
			'label'	  =>  'cPlatform TV Series XML',
			'rows' => 8
		));
		
		$this->addMetadataFieldsAsValues('c_platform_tv_series_field');
		
		$this->addDisplayGroup(
			array('feed_title', 'feed_link', 'feed_description', 'feed_last_build_date', 'cplatform_xml', 'c_platform_tv_series_field', 'should_include_captions', 'should_include_cue_points', 'should_add_thumb_extension'), 
			'feed', 
			array('legend' => 'Feed Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_url'), 
			'feed_url_group', 
			array('legend' => '', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
	
	public function renderFeedUrl($content)
	{
		$url = $this->getValue('feed_url');
		if (!$url)
			return 'Feed URL will be generated once the feed is saved';
		else
			return '<a href="'.$url.'" target="_blank">Feed URL</a>';
	}
	
	public function addMetadataFieldsAsValues($elementName)
	{
		$this->getElement($elementName)->clearMultiOptions();
		Infra_ClientHelper::impersonate($this->partnerId);
		$client = Infra_ClientHelper::getClient();
		$metadataPlugin = Kaltura_Client_Metadata_Plugin::get($client);
		$profileListResponse = $metadataPlugin->metadataProfile->listAction();
		$metadataFields = array();
		foreach($profileListResponse->objects as $profile)
		{
			$doc = new DOMDocument();
			$doc->loadXML($profile->xsd);
			$xpath = new DOMXPath($doc);
			$xpath->registerNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
			$nameNodes = $xpath->query('//xsd:element//xsd:element/@name');
			foreach($nameNodes as $nameNode)
			{
				$metadataFields[$profile->id.':'.$nameNode->nodeValue] = $profile->name . ' > ' . $nameNode->nodeValue;
			}
		}
		Infra_ClientHelper::unimpersonate();
		$this->getElement($elementName)->addMultiOptions(array('' => ''));
		$this->getElement($elementName)->addMultiOptions($metadataFields);
	}
	
	public function render(Zend_View_Interface $view = null)
	{
		$this->disableTriggerUpdateFieldConfig();
		
		return parent::render($view);
	}
	
	public function disableTriggerUpdateFieldConfig()
	{
		$subForm = $this->getSubForm('fieldConfigArray');
		if ($subForm)
		{
			$fieldsSubForms = $subForm->getSubForms();
			foreach($fieldsSubForms as $fieldSubForm)
			{
				$updateOnChange = $fieldSubForm->getElement('updateOnChange');
				if ($updateOnChange)
				{
					$updateOnChange->setAttrib('disabled', 'disabled');
				}
			}
		}
	}
}
