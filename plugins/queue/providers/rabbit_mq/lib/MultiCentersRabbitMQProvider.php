<?php

/**
 * @package plugins.rabbitMQ
 * @subpackage lib.enum
 */
class MultiCentersRabbitMQProvider extends QueueProvider
{
	/**
	 *
	 * @var array<RabbitMQProvider>
	 */
	protected $providers = array();
	public function __construct(array $rabbitConfigs, $constructorArgs)
	{
		foreach($rabbitConfigs as $section => $rabbitConfig)
		{
			if(is_numeric($section)) 
			{
				$this->providers[] = new RabbitMQProvider($rabbitConfig, $constructorArgs);
			}
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::exists()
	 */
	public function exists($queueName)
	{
		foreach($this->providers as $provider)
		{
			/* @var $provider RabbitMQProvider */
			if($provider->exists($queueName))
			{
				return true;
			}
		}
		
		return false;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::create()
	 */
	public function create($queueName)
	{
		foreach($this->providers as $provider)
		{
			/* @var $provider RabbitMQProvider */
			try
			{
				$provider->create($queueName);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e);
			}
		}
	}
	
	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::send()
	 */
	public function send($queueName, $data)
	{
		foreach($this->providers as $provider)
		{
			/* @var $provider RabbitMQProvider */
			try
			{
				$provider->send($queueName, $data);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e);
			}
		}
	}
}
