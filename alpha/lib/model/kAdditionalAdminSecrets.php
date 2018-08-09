<?php
/**
 * @package Core
 * @subpackage model
 */
class kAdditionalAdminSecrets
{

	/**
	 * @var array enableAdminSecret
	 */
	private $enableAdminSecrets;


	/**
	 * @var array disableAdminSecret
	 */
	private $disableAdminSecrets;

	/**
	 * @return array
	 */
	public function getEnableAdminSecrets()
	{
		return $this->enableAdminSecrets;
	}

	/**
	 * @param array $enableAdminSecrets
	 */
	public function setEnableAdminSecrets(array $enableAdminSecrets)
	{
		$this->enableAdminSecrets = $enableAdminSecrets;
	}

	/**
	 * @return array
	 */
	public function getDisableAdminSecrets()
	{
		return $this->disableAdminSecrets;
	}

	/**
	 * @param array $disableAdminSecrets
	 */
	public function setDisableAdminSecrets(array $disableAdminSecrets)
	{
		$this->disableAdminSecrets = $disableAdminSecrets;
	}

	/**
	 * move secret to enable
	 * @param string $adminSecret
	 */
	public function enableAdminSecret(string $adminSecret)
	{
		if (($key = array_search($adminSecret, $this->disableAdminSecrets)) !== false) {
			unset($this->disableAdminSecrets[$key]);
		}
		$this->enableAdminSecrets[] = $adminSecret;
	}

	/**
	 * move secret to disable
	 * @param string $adminSecret
	 */
	public function disableAdminSecret(string $adminSecret)
	{
		if (($key = array_search($adminSecret, $this->enableAdminSecrets)) !== false) {
			unset($this->enableAdminSecrets[$key]);
		}
		$this->disableAdminSecrets[] = $adminSecret;
	}

}