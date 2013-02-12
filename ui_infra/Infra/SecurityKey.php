<?php
/**
 * @package UI-infra
 * @subpackage forms
 */
class Infra_SecurityKey extends Zend_Validate_Abstract 
{
	const WRONG_KEY = 'WRONG_KEY';
	
	/* (non-PHPdoc)
	 * @see Zend_Validate_Abstract::$_messageTemplates
	 */
	protected $_messageTemplates = array(self::WRONG_KEY => "Form could not be submitted from external servers");
	
	/**
	 * Class name of the calling form, used for the session name space
	 * 
	 * @var string
	 */
	protected $formType;
	
	public function __construct($formType)
	{
		$this->formType = $formType;
	}
	
	/**
	 * @return Zend_Session_Namespace
	 */
	protected function getSession()
	{
		return new Zend_Session_Namespace(get_class($this) . '_' . $this->formType);
	}
	
	/**
	 * @return string
	 */
	public function getKey()
	{
		$key = uniqid('k');
		$session = self::getSession();
		$session->key = $key;
		return $key;
	}
	
	/* (non-PHPdoc)
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value) 
	{
		$this->_setValue($value);
		
		$session = self::getSession();
		if($value == $session->key)
			return true;
			
		throw new Infra_Exception('Form could not be generated and submitted from different sessions', Infra_Exception::ERROR_CODE_WRONG_FORM_KEY);
	}
}