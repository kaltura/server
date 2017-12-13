<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_OutputFormatsSubForm extends ConfigureSubForm
{
	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "OutputFormat_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmOutputFormatsSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'output-format-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}

	public function isValid($data)
	{
		if ($data['OutputFormats'])
			return true;
		else return false;
	}
}