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

	}

	public function isValid($data)
	{
		if ($data['VendorProfileRules'])
			return true;
		else
			return false;
	}

}