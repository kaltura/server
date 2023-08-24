<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PasswordValidationRules extends Infra_Form
{
	/**
	 * @var int
	 */
	protected $index = null;
	
	public function __construct($options = null, $index = null)
	{
		parent::__construct($options);
		if ($index)
		{
			$this->index = $index;
		}
	}
	
	public function init()
	{
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'password_validation_rule_item.phtml',
		));
		
		$this->addElement('text', 'regex', array(
			'filters' 		=> array('StringTrim'),
			//'label'			=> 'Regex:',
			'decorators'	=> array('ViewHelper'),
			//'decorators'	=> array('ViewHelper', array('HtmlTag',  array('id' => 'regex'))),
		));
		
		$this->addElement('text', 'description', array(
			'filters' 		=> array('StringTrim'),
			//'label'			=> 'Description:',
			'decorators'	=> array('ViewHelper'),
			//'decorators'	=> array('ViewHelper', array('HtmlTag',  array('id' => 'description'))),
		));
		
		$this->addElement('hidden', 'belongs', array(
			'decorators'	=> array('ViewHelper'),
		));
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		/** @var $object KalturaRegexItem */
		parent::populateFromObject($object, $add_underscore);
	}
}