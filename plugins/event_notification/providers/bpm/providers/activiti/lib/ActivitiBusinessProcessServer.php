<?php


/**
 * @package plugins.activitiBusinessProcessNotification
 * @subpackage model
 */
class ActivitiBusinessProcessServer extends BusinessProcessServer {

	const CUSTOM_DATA_HOST = 'host';
	const CUSTOM_DATA_PORT = 'port';
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';

	public function getHost()									{return $this->getFromCustomData(self::CUSTOM_DATA_HOST);}
	public function getPort()									{return $this->getFromCustomData(self::CUSTOM_DATA_PORT);}
	public function getProtocol()								{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL);}
	public function getUsername()								{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()								{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}

	public function setHost($v)									{return $this->putInCustomData(self::CUSTOM_DATA_HOST, $v);}
	public function setPort($v)									{return $this->putInCustomData(self::CUSTOM_DATA_PORT, $v);}
	public function setProtocol($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v);}
	public function setUsername($v)								{return $this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
}
