<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_RulesSubForm extends ConfigureSubForm
{
	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "Rule_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
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

		$options = array(
			'filters' 		=> array('StringTrim'),
			'placement'		=> 'prepend',
		);
			$options["hidden"] = true;

		$this->addElement("text", $this->prefix."description", $options);
	}

	public function isValid($data)
	{
		return ($data['ReachProfileRules']);
	}

}