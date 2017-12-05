<?php
//
///**
// * @package plugins.reach
// * @subpackage Admin
// */
//
//class ConfigureSubForm extends Zend_Form_SubForm
//{
//
//	public function __construct()
//	{
//		parent::__construct();
//	}
//
//	protected function addObjectSection($name, $obj, $ignore, $prefix) {
//		$tag = str_replace(' ', '', $name);
//		$this->addElement('hidden', "crossLine_$tag", array(
//			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
//		));
//		$this->addTitle($name);
//		$this->addObjectProperties($obj, $ignore, $prefix);
//		return;
//
//	}
//
//	protected function addObjectProperties($obj, $ignore, $prefix) {
//		$reflectClass = new ReflectionClass(get_class($obj));
//		$properties = $reflectClass	->getProperties(ReflectionProperty::IS_PUBLIC);
//
//		foreach($properties as $property) {
//			if (!in_array($property->name, $ignore)) {
//				$type = $this->getTypeFromDoc($property->getDocComment());
//				$this->addElementByType($type, $property->name, $prefix);
//			}
//		}
//	}
//
//	protected function getTypeFromDoc($docComment) {
//		$exp = "/\\@var (.*)/";
//		$result = null;
//		$lines = explode("\n", $docComment);
//		foreach ($lines as $line)
//			if (preg_match( $exp, $line, $result ))
//				return $result[1];
//		return 'string'; //as default
//	}
//
//	protected function addElementByType($type, $name, $prefix) {
//		switch($type) {
//			case 'int':
//				return $this->addIntegerElement($name, $prefix);
//			case 'string':
//				return $this->addStringElement($name, $prefix);
//
//			case 'bool':
//			case 'boolean':
//				return $this->addBooleanElement($name, $prefix);
//
//			default:
//				if (strpos($type ,'Enum') > -1)
//					return $this->addEnumElement($name, $prefix, $type);
//				if (strpos($type ,'array') > -1)
//					return $this->addArrayElement($name, $prefix);
//		}
//		return null;
//	}
//
//	protected function addStringElement($name, $prefix)
//	{
//		$params = array('required' => false, 'value'=>	'N/A',);
//		$this->addElementByStrType('text', $name, "$prefix$name", $params);
//	}
//
//	protected function addIntegerElement($name, $prefix) {
//		$params = array('required' => false, 'value'=>	-1, 'innerType' => 'integer', 'oninput'	=> 'checkNumValid(this.value)');
//		$this->addElementByStrType('text', $name, "$prefix$name", $params);
//	}
//
//	protected function addStringAreaElement($name, $prefix) {
//		$params = array('required' => false, 'value'=>	'N/A',);
//		$this->addElementByStrType('textarea', $name, "$prefix$name", $params);
//	}
//
//	protected function addArrayElement($name, $prefix) {
//		$this->addElement('textarea', "$prefix$name", array(
//			'label' 		=> "$name (Array: enter item comma separated. for key-value insert: key:value)",
//			'required'		=> false,
//			'filters'		=> array('StringTrim'),
//			'value'			=>	'N/A',
//		));
//	}
//
//	protected function addBooleanElement($name, $prefix) {
//		$this->addElement('checkbox', "$prefix$name", array(
//			'label'	  => $name,
//			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
//
//		));
//	}
//
//	protected function addEnumElement($name, $prefix, $enumClass) {
//		$elem = new Kaltura_Form_Element_EnumSelect("$prefix$name", array(
//			'enum' => $enumClass,
//			'excludes' => array()
//		));
//		$elem->addMultiOption("N/A", "NONE");
//		$elem->setValue("N/A");
//		$elem->setLabel("$name:");
//		$elem->setRequired(true);
//		$this->addElement($elem);
//
//		$this->removeClassName("$prefix$name");
//	}
//
//	protected function removeClassName($name) {
//		$elem = $this->getElement($name);
//		foreach($elem->options as &$option){
//			$newOpArr = explode('::', $option);
//			if (count($newOpArr) > 1)
//				$option = $newOpArr[1];
//		}
//	}
//
//	protected function addComment($name, $msg) {
//		$element = new Zend_Form_Element_Hidden($name);
//		$element->setLabel($msg);
//		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dd', 'class' => 'comment'))));
//		$this->addElements(array($element));
//	}
//
//	protected function addTitle($name, $tag = null)
//	{
//		if (!$tag)
//			$tag = str_replace(' ', '', $name);
//		$titleElement = new Zend_Form_Element_Hidden("Title_$tag");
//		$titleElement->setLabel($name);
//		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
//		$this->addElement($titleElement);
//	}
//
//	protected function addTextElement($label, $tag, $params = array()) {
//		$this->addElementByStrType('text', $label, $tag, $params);
//	}
//
//	protected function addElementByStrType($type, $label, $tag, $params) {
//		$options = array(
//			'label' 		=> $label,
//			'filters' 		=> array('StringTrim'),
//			'placement'		=> 'prepend',
//		);
//		foreach($params as $key => $val)
//			$options[$key] = $val;
//
//		$this->addElement($type, $tag, $options);
//	}
//
//}
