<?php

class KafkaProvider extends QueueProvider
{
	protected $topic = null;
	protected $producer = null;
	
	const DEFAULT_PORT = 29092;
	const MAX_RETRIES = 3;
	
	public function __construct(array $kafkaConfig)
	{
		$brokersArray = array();
		if (isset($kafkaConfig['brokers']))
		{
			$brokers = $kafkaConfig['brokers'];
			$brokers = explode(",", $brokers);
			
			foreach ($brokers as $broker)
			{
				list($host, $port) = explode(":", $broker);
				if(!$port)
				{
					$port = self::DEFAULT_PORT;
				}
				
				$brokersArray[] = "$host:$port";
			}
		}
		else
		{
			$server = $kafkaConfig['server'];
			$port = $kafkaConfig['port'];
		}
		
		$conf = new RdKafka\Conf();
		$conf->set('log_level', (string)LOG_DEBUG);
		if ($server)
		{
			$conf->set('metadata.broker.list', $server . ':' . $port);
		}
		
		if (isset($kafkaConfig['username']) && isset($kafkaConfig['password']))
		{
			$conf->set('sasl.username', $kafkaConfig['username']);
			$conf->set('sasl.password', $kafkaConfig['password']);
		}
		
		$producer = new RdKafka\Producer($conf);
		if (count($brokersArray))
		{
			$producer->addBrokers(implode(',', $brokersArray));
		}
		
		$this->producer = $producer;
		KalturaLog::log("Kafka connection Initialized");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::send()
	 */
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::send()
	 */
	public function send($topicName, $message, $msgArgs = array())
	{
		for ($retry = 1; ; $retry++)
		{
			try
			{
				$this->create($topicName);
				$this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $msgArgs["partitionKey"]);
				$this->producer->poll(0);
				$result = $this->producer->flush(500);
				break;
			}
			catch (Exception $e)
			{
				if ($retry == self::MAX_RETRIES)
				{
					throw $e;
				}
			}
		}
		
		if (!RD_KAFKA_RESP_ERR_NO_ERROR === $result)
		{
			KalturaLog::err("producing kafka msg failed");
		}
		
		//$this->producer->purge(RD_KAFKA_PURGE_F_QUEUE);
	}
	
	public function produce($topic, $partitionKey, $kafkaPayload)
	{
		for ($retry = 1; ; $retry++)
		{
			try
			{
				$this->topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($kafkaPayload));
				$this->producer->poll(0);
				$result = $this->producer->flush(500);
				break;
			}
			catch (Exception $e)
			{
				if ($retry == self::MAX_RETRIES)
				{
					throw $e;
				}
			}
		}
		
		if (!RD_KAFKA_RESP_ERR_NO_ERROR === $result)
		{
			KalturaLog::err("producing kafka msg failed");
		}
	}
	
	public function getTopic($queueName)
	{
		$metaData = $this->producer->getMetadata(true, null, 1000);
		$topics = $metaData->getTopics();
		foreach ($topics as $topic)
		{
			if ($topic->getTopic() == $queueName)
			{
				return sizeof($topic->getPartitions());
			}
			
		}
		
		return 0;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::exists()
	 */
	public function exists($queueName)
	{
		$metaData = $this->producer->getMetadata(true, null, 1000);
		$topics = $metaData->getTopics();
		foreach ($topics as $topic)
		{
			if ($topic->getTopic() == $queueName)
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::create()
	 */
	public function create($queueName)
	{
		$topic = $this->producer->newTopic($queueName);
		$this->topic = $topic;
	}
	
	public function __destruct()
	{
		$this->producer->flush(500);
	}
	
}
