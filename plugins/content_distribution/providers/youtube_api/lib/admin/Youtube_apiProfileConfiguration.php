<?php 
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage admin
 */
class Form_Youtube_apiProfileConfiguration extends Form_ProviderProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof Kaltura_Client_Youtube_apiDistribution_Type_Youtube_apiDistributionProfile)
		{
			$requiredFlavorParamsIds = explode(',', $object->requiredFlavorParamsIds);
			$optionalFlavorParamsIds = explode(',', $object->optionalFlavorParamsIds);
			
			if($object->movFlavorParamsId)
			{
				if(!in_array($object->movFlavorParamsId, $requiredFlavorParamsIds))
					$requiredFlavorParamsIds[] = $object->movFlavorParamsId;
					
				$flavorKey = array_search($object->movFlavorParamsId, $optionalFlavorParamsIds);
				if($flavorKey !== false)
					unset($optionalFlavorParamsIds[$flavorKey]);
			}
			
			if($object->flvFlavorParamsId)
			{
				if(!in_array($object->flvFlavorParamsId, $requiredFlavorParamsIds))
					$requiredFlavorParamsIds[] = $object->flvFlavorParamsId;
					
				$flavorKey = array_search($object->flvFlavorParamsId, $optionalFlavorParamsIds);
				if($flavorKey !== false)
					unset($optionalFlavorParamsIds[$flavorKey]);
			}
			
			if($object->wmvFlavorParamsId)
			{
				if(!in_array($object->wmvFlavorParamsId, $requiredFlavorParamsIds))
					$requiredFlavorParamsIds[] = $object->wmvFlavorParamsId;
					
				$flavorKey = array_search($object->wmvFlavorParamsId, $optionalFlavorParamsIds);
				if($flavorKey !== false)
					unset($optionalFlavorParamsIds[$flavorKey]);
			}
			
			$object->requiredFlavorParamsIds = implode(',', $requiredFlavorParamsIds);
			$object->optionalFlavorParamsIds = implode(',', $optionalFlavorParamsIds);
		}
		return $object;
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YouTube Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		// General
		$this->addElement('text', 'username', array(
			'label'			=> 'YouTube Account:',
			'filters'		=> array('StringTrim'),
		));

		// General
		$this->addElement('text', 'password', array(
			'label'			=> 'YouTube Password:',
			'filters'		=> array('StringTrim'),
		));
								
//		$this->addMetadataProfile();
		
		$this->addDisplayGroup(
			array('username', 'password',  'metadata_profile_id'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
				
		//  Metadata
		$this->addElement('text', 'default_category', array(
			'label' => 'Default Category:',
		));
		
		$this->addDisplayGroup(
			array('default_category'), 
			'metadata',
			array('legend' => 'Metadata', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		// Community
		$this->addElement('select', 'allow_comments', array(
			'label' => 'Allow Comments:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addElement('select', 'allow_embedding', array(
			'label' => 'Allow Embedding:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_ratings', array(
			'label' => 'Allow Ratings:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_responses', array(
			'label' => 'Allow Responses:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addDisplayGroup(
			array('allow_comments', 'allow_embedding', 'allow_ratings', 'allow_responses'), 
			'community', 
			array('legend' => 'Community', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
}