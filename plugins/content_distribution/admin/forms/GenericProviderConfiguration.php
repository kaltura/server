<?php 
class Form_GenericProviderConfiguration extends Kaltura_Form
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);

		$requiredFlavorParamsIds = array();
		$optionalFlavorParamsIds = array();
		$object->requiredThumbDimensions = array();
		$object->optionalThumbDimensions = array();
		
		foreach($properties as $property => $value)
		{
			$matches = null;
			if(preg_match('/flavorParamsId_(\d+)$/', $property, $matches))
			{
				$flavorId = $matches[1];
				if($value == 'required')
					$requiredFlavorParamsIds[] = $flavorId;
				if($value == 'optional')
					$optionalFlavorParamsIds[] = $flavorId;
			}
			
			if(preg_match('/dimensionsWidth_(\d+)$/', $property, $matches))
			{
				$thumbIndex = $matches[1];
				
				$dimensions = new KalturaDistributionThumbDimensions();
				$dimensions->width = $value;
				$dimensions->height = $properties["dimensionsHeight_{$thumbIndex}"];
				
				if($properties["dimensionsRequired_{$thumbIndex}"])
					$object->requiredThumbDimensions[] = $dimensions;
				else
					$object->optionalThumbDimensions[] = $dimensions;
			}
		}
		$object->requiredFlavorParamsIds = implode(',', $requiredFlavorParamsIds);
		$object->optionalFlavorParamsIds = implode(',', $optionalFlavorParamsIds);
		
		if(isset($properties['dimensionsWidth']) && is_array($properties['dimensionsWidth']))
		{
			foreach($properties['dimensionsWidth'] as $index => $dimensionsWidth)
			{
				$dimensionsHeight = $properties['dimensionsHeight'][$index];
				$dimensionsRequired = $properties['dimensionsRequired'][$index];
				
				$dimensions = new KalturaDistributionThumbDimensions();
				$dimensions->width = $dimensionsWidth;
				$dimensions->height = $dimensionsHeight;
				
				if($dimensionsRequired)
					$object->requiredThumbDimensions[] = $dimensions;
				else
					$object->optionalThumbDimensions[] = $dimensions;
			}
		}
		
		return $object;
	}
	
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmGenericProviderConfig');

		$this->setDescription('generic-provider-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('hidden', 'crossLine1', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addElement('checkbox', 'is_default', array(
			'label'	  => 'Is Default',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
		));
	}
	
	public function addFlavorParamsFields(KalturaFlavorParamsListResponse $flavorParams, array $optionalFlavorParamsIds = array(), array $requiredFlavorParamsIds = array())
	{
		$this->addElement('hidden', 'crossLine2', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element = new Zend_Form_Element_Hidden('setFlavorParams');
		$element->setLabel('Flavor Params');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		foreach($flavorParams->objects as $index => $flavorParamsItem)
		{
			$element = $this->createElement('radio', 'flavorParamsId_' . $flavorParamsItem->id);
			$element->setLabel($flavorParamsItem->name);
			$element->setDecorators(
				array(
					'ViewHelper', 
					array('Label', array('placement' => 'prepend')),
					array('HtmlTag',  array('tag' => 'div')),
				));
			$element->setSeparator('');
			
			$element->addMultiOption("", 'None');
			$element->addMultiOption("optional", 'Optional');
			$element->addMultiOption("required", 'Required');
			
			if(in_array($flavorParamsItem->id, $requiredFlavorParamsIds))
				$element->setValue('required');
			elseif(in_array($flavorParamsItem->id, $optionalFlavorParamsIds))
				$element->setValue('optional');
			else
				$element->setValue('');
			
			$this->addElement($element);
		}
		
		$this->addElement('hidden', 'crossLine3', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
	}
	
	private $dimensionsCounter = 0;
	public function addThumbDimensions(KalturaDistributionThumbDimensions $dimensions, $isRequired)
	{
		if(!$this->dimensionsCounter)
		{
			$element = new Zend_Form_Element_Hidden('setThumbnailDimensions');
			$element->setLabel('Thumbnail Dimensions');
			$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
			$this->addElements(array($element));
		}
		
		$this->addElement('text', 'dimensionsWidth_' . $this->dimensionsCounter, array(
			'label'			=> 'Width:',
			'value'			=> $dimensions->width,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'dimensionsHeight_' . $this->dimensionsCounter, array(
			'label'			=> 'Height:',
			'value'			=> $dimensions->height,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'dimensionsRequired_' . $this->dimensionsCounter, array(
			'label'	  => 'Is Required',
			'value'	  => $isRequired,
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
		));
			
		$this->addElement('button', 'removeThumbDimensions_' . $this->dimensionsCounter, array(
			'label'		=> 'Remove',
			'onclick'		=> "removeThumbDimensions({$this->dimensionsCounter})",
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addDisplayGroup(
			array('dimensionsWidth_' . $this->dimensionsCounter, 'dimensionsHeight_' . $this->dimensionsCounter, 'dimensionsRequired_' . $this->dimensionsCounter, 'removeThumbDimensions_' . $this->dimensionsCounter), 
			'thumbDimensions_' . $this->dimensionsCounter,
			array(
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag',array('tag'=>'div', 'class' => 'thumbDimensions_' . $this->dimensionsCounter))),
			)
		);
		
		$this->dimensionsCounter++;
	}
	
	public function addThumbDimensionsForm()
	{
		$this->addElement('button', 'newThumbDimensionsButton', array(
			'label'		=> 'New Dimensions',
			'onclick'		=> "newThumbDimensions()",
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addDisplayGroup(
			array('newThumbDimensionsButton'), 
			'newThumbDimensionsButtonGroup',
			array(
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag',array('tag'=>'div'))),
			)
		);
		
		$this->addElement('text', 'dimensionsWidth', array(
			'label'			=> 'Width:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'dimensionsHeight', array(
			'label'			=> 'Height:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'dimensionsRequired', array(
			'label'	  => 'Is Required',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
		));
			
		$this->addDisplayGroup(
			array('dimensionsWidth', 'dimensionsHeight', 'dimensionsRequired'), 
			'newThumbDimensions',
			array(
				'legend' => 'New Thumbnail Dimensions',
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag',array('tag'=>'div','style'=>'display: none;', 'class' => 'newThumbDimensions'))),
			)
		);
	}
	
	public function addProviderActions()
	{
		$this->addProviderAction('submit');
		$this->addProviderAction('update');
		$this->addProviderAction('delete');
		$this->addProviderAction('fetchReport');
	}
	
	public function addProviderAction($action)
	{
		$this->addElement('checkbox', "actionEnabled-$action", array(
			'label'	  =>  'Enabled',
			'onchange'		=> "actionEnabledChanged('$action')",
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => "actionEnabled-$action")))
		));
			
		$this->addElement('select', "actionProtocol-$action", array(
			'label'	  =>  'Protocol',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$element = $this->getElement("actionProtocol-$action");
		$element->addMultiOption(KalturaDistributionProtocol::FTP, 'FTP');
		$element->addMultiOption(KalturaDistributionProtocol::SFTP, 'SFTP');
		$element->addMultiOption(KalturaDistributionProtocol::SCP, 'SCP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTP, 'HTTP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTPS, 'HTTPS');
			
		$this->addElement('text', "serverAddress-$action", array(
			'label'	  =>  'Server Address',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remotePath-$action", array(
			'label'	  =>  'Remote Path',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remoteUsername-$action", array(
			'label'	  =>  'Remote Username',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remotePassword-$action", array(
			'label'	  =>  'Remote Password',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "mrssTransformer-$action", array(
			'label'	  =>  'MRSS Transformer (XSL)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "mrssValidator-$action", array(
			'label'	  =>  'MRSS Validator (XSD)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "resultsTransformer-$action", array(
			'label'	  =>  'Results Transformer (XSL)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addDisplayGroup(
			array(
				"actionEnabled-$action", 
				"actionProtocol-$action",
				"serverAddress-$action", 
				"remotePath-$action", 
				"remoteUsername-$action", 
				"remotePassword-$action", 
				"mrssTransformer-$action",
				"mrssValidator-$action",
				"resultsTransformer-$action",
			), 
			"actionGroup-$action",
			array(
				'legend' => ucfirst($action) . ' Action',
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag',array('class' => "actionGroup-$action"))),
			)
		);
	}
}