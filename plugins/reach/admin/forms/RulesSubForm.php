<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_RulesSubForm extends ConfigureSubForm
{
	private $ignore = array('relatedObjects', 'type', 'gs');
	private $ignoreCondition = array('relatedObjects', 'type','not');
	private $prefix = "Rule_";

	private $type;
	private $condition;

	public function __construct($type, $condition)
	{
		$this->type = $type;
		$this->condition = $condition;
		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmRulesSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'rule-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addStringElement("ruleType", $this->prefix);
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);

		$objCondition = new $this->condition();
		$this->addObjectProperties($objCondition, $this->ignoreCondition, $this->prefix);

		$options = array(
			'filters' 		=> array('StringTrim'),
			'placement'		=> 'prepend',
		);
			$options["hidden"] = true;

		$this->addElement("text", $this->prefix."description", $options);
	}

	public function isValid($data)
	{
		if(!$data['ReachProfileRules'])
			return true;
		
		$jsonData = json_decode($data['ReachProfileRules'], true);
		if(!empty($jsonData))
			return true;
		
		return false;
	}

}