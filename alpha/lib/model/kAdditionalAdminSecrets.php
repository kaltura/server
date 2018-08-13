<?php
/**
 * @package Core
 * @subpackage model
 */
class kAdditionalAdminSecrets
{

	/**
	 * @var array
	 */
	private $enabledAdminSecrets;


	/**
	 * @var array
	 */
	private $disabledAdminSecrets;

	public function __construct()
	{
		$this->enabledAdminSecrets = array();
		$this->disabledAdminSecrets = array();
	}


	/**
	 * @return array
	 */
	public function getEnabledAdminSecrets()
	{
		return $this->enabledAdminSecrets;
	}

	/**
	 * @param array $enabledAdminSecrets
	 */
	public function setEnabledAdminSecrets(array $enabledAdminSecrets)
	{
		$this->enabledAdminSecrets = $enabledAdminSecrets;
	}

	/**
	 * @return array
	 */
	public function getDisabledAdminSecrets()
	{
		return $this->disabledAdminSecrets;
	}

	/**
	 * @param array $disabledAdminSecrets
	 */
	public function setDisabledAdminSecrets(array $disabledAdminSecrets)
	{
		$this->disabledAdminSecrets = $disabledAdminSecrets;
	}

	/**
	 * move secret to enable
	 * @param string $adminSecret
	 */
	public function enableAdminSecret(string $adminSecret)
	{
		if (($key = array_search($adminSecret, $this->disabledAdminSecrets)) !== false) {
			unset($this->disabledAdminSecrets[$key]);
		}
		$this->enabledAdminSecrets[] = $adminSecret;
	}

	/**
	 * move secret to disable
	 * @param string $adminSecret
	 */
	public function disableAdminSecret(string $adminSecret)
	{
		if (($key = array_search($adminSecret, $this->enabledAdminSecrets)) !== false) {
			unset($this->enabledAdminSecrets[$key]);
		}
		$this->disabledAdminSecrets[] = $adminSecret;
	}

}