<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_AdvanceSearchSubForm extends ConfigureSubForm
{
	const Search_PARAMS_PREFIX = 'searchParams_';

	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "Cond_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}

	public function init()
	{
		$this->setAttrib('id', 'frmAdvanceSearchSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'condition-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addStringElement("conditionType", $this->prefix);
		$this->addStringElement("MetadataProfileId", $this->prefix);
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}
	
}