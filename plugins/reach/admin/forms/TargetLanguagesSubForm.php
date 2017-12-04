<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_TargetLanguagesSubForm extends ConfigureSubForm
{
	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "TargetLanguage_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmTargetLanguagesSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'target-language-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}

	public function isValid($data)
	{
		if ($data['TargetLanguages'])
			return true;
		else return false;
	}
}