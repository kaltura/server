<?php

/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */

class ConfigureForm extends Infra_Form
{

	public function __construct()
	{
		parent::__construct();
	}

	protected function addObjectSection($name, $obj, $ignore, $prefix)
	{
		$this->addLine($name);
		$this->addTitle($name);
		$this->addObjectProperties($obj, $ignore, $prefix);
	}

	protected function addObjectProperties($obj, $ignore, $prefix) {
		$reflectClass = new ReflectionClass(get_class($obj));
		$properties = $reflectClass	->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach($properties as $property) {
			if (!in_array($property->name, $ignore)) {
				$type = $this->getTypeFromDoc($property->getDocComment());
				$this->addElementByType($type, $property->name, $prefix);
			}
		}
	}

	protected function getTypeFromDoc($docComment) {
		$exp =  "/\\@var (\\w*)/";
		$result = null;
		$lines = explode("\n", $docComment);
		foreach ($lines as $line)
			if (preg_match( $exp, $line, $result ))
				return $result[1];
		return 'string'; //as default
	}

	protected function addElementByType($type, $name, $prefix) {
		switch($type) {
			case 'int':
			case 'string':
				return $this->addStringElement($name, $prefix);
			case 'bool':
			case 'boolean':
				return $this->addBooleanElement($name, $prefix);

			default:
				if (strpos($type ,'Enum') > -1)
					return $this->addEnumElement($name, $prefix, $type);
		}
		return null;
	}

	protected function addStringElement($name, $prefix) {
		$this->addElement('text', "$prefix$name", array(
			'label' 		=> $name,
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		$this->getElement("$prefix$name")->setValue("N/A");
	}


	protected function addBooleanElement($name, $prefix) {
		$this->addElement('checkbox', "$prefix$name", array(
			'label'	  => $name,
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
	}
	
	protected function addSelectElement($name, $options, $prefix = '') 
	{
		$options['N/A'] = 'None';
		$this->addElement('select', "$prefix$name", array(
			'label'			=> "$name:",
			'filters'		=> array('StringTrim'),
			'multiOptions'	=> $options,
		));
		$this->getElement("$prefix$name")->setValue("N/A");
	}

	protected function addEnumElement($name, $prefix, $enumClass) {
		$elem = new Kaltura_Form_Element_EnumSelect("$prefix$name", array(
			'enum' => $enumClass,
			'excludes' => array(),
			'value' => 'default'
		));
		$elem->addMultiOption("N/A", "NONE");
		$elem->setValue("N/A");
		$elem->setLabel("$name:");
		$elem->setRequired(true);
		$this->addElement($elem);

		$this->removeClassName("$prefix$name");
	}

	protected function removeClassName($name) {
		$elem = $this->getElement($name);
		foreach($elem->options as &$option){
			$newOpArr = explode('::', $option);
			if (count($newOpArr) > 1)
				$option = $newOpArr[1];
		}
	}

	protected function addComment($name, $msg) {
		$element = new Zend_Form_Element_Hidden($name);
		$element->setLabel($msg);
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'partnerConfigurationDescription'))));
		$this->addElements(array($element));
	}

	protected function addTitle($name)
	{
		$tag = str_replace(' ', '', $name);
		$titleElement = new Zend_Form_Element_Hidden($tag);
		$titleElement->setLabel($name);
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);
	}

	protected function addLine($name)
	{
		$tag = str_replace(' ', '', $name);
		$this->addElement('hidden', "crossLine_$tag", array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
	}

}