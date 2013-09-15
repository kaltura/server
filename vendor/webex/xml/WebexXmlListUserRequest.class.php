<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlListUser.class.php');
require_once(__DIR__ . '/WebexXmlComEmailType.class.php');
require_once(__DIR__ . '/WebexXmlUseActiveType.class.php');
require_once(__DIR__ . '/WebexXmlServListControlType.class.php');
require_once(__DIR__ . '/WebexXmlUseOrderType.class.php');
require_once(__DIR__ . '/WebexXmlUseDataScopeType.class.php');

class WebexXmlListUserRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $firstName;
	
	/**
	 *
	 * @var string
	 */
	protected $lastName;
	
	/**
	 *
	 * @var WebexXmlComEmailType
	 */
	protected $email;
	
	/**
	 *
	 * @var WebexXmlUseActiveType
	 */
	protected $active;
	
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlServListControlType
	 */
	protected $listControl;
	
	/**
	 *
	 * @var WebexXmlUseOrderType
	 */
	protected $order;
	
	/**
	 *
	 * @var WebexXmlUseDataScopeType
	 */
	protected $dataScope;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'firstName',
			'lastName',
			'email',
			'active',
			'webExId',
			'listControl',
			'order',
			'dataScope',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:lstUser';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlListUser';
	}
	
	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @param WebexXmlComEmailType $email
	 */
	public function setEmail(WebexXmlComEmailType $email)
	{
		$this->email = $email;
	}
	
	/**
	 * @param WebexXmlUseActiveType $active
	 */
	public function setActive(WebexXmlUseActiveType $active)
	{
		$this->active = $active;
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @param WebexXmlServListControlType $listControl
	 */
	public function setListControl(WebexXmlServListControlType $listControl)
	{
		$this->listControl = $listControl;
	}
	
	/**
	 * @param WebexXmlUseOrderType $order
	 */
	public function setOrder(WebexXmlUseOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @param WebexXmlUseDataScopeType $dataScope
	 */
	public function setDataScope(WebexXmlUseDataScopeType $dataScope)
	{
		$this->dataScope = $dataScope;
	}
	
}
		
