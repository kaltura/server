<?php
require_once(__DIR__ . '/ActivitiClient.php');

class ActivitiService
{
	/**
	 * @var ActivitiClient
	 */
	protected $client;
	
	/**
	 * @param ActivitiClient $client
	 */
	public function __construct(ActivitiClient $client)
	{
		$this->client = $client;
	}
}
