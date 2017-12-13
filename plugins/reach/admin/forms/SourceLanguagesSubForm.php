<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_SourceLanguagesSubForm extends ConfigureSubForm
{
	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "SourceLanguage_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmSourceLanguagesSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'source-language-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}

	public function isValid($data)
	{
		if ($data['SourceLanguages'])
			return true;
		else return false;
	}
}