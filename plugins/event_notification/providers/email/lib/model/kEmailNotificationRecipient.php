<?php
/**
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationRecipient
{
	/**
	 * Recipient e-mail address
	 * @var kStringValue
	 */
	protected $email;
	
	/**
	 * Recipient name
	 * @var kStringValue
	 */
	protected $name;
	
	/**
	 * @return kStringValue $email
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @return kStringValue $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param kStringValue $email
	 */
	public function setEmail(kStringValue $email)
	{
		$this->email = $email;
	}

	/**
	 * @param kStringValue $name
	 */
	public function setName(kStringValue $name)
	{
		$this->name = $name;
	}
}