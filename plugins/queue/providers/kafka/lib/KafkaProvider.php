<?php

class KafkaProvider extends QueueProvider
{
	protected $topic = null;
	protected $producer = null;
	protected $flushTtl = null;
	protected $brokers = '';

	const DEFAULT_FLUSH_TTL = 500;
	const DEFAULT_PORT = 29092;
	const MAX_RETRIES = 3;
	const KAFKA_ACTION_SEND_MESSAGE = 'send_message';
	const KAFKA_ACTION_CONNECT_TOPIC = 'connect_topic';

	public function __construct(array $kafkaConfig)
	{
		if (!isset($kafkaConfig['brokers']) && !(isset($kafkaConfig['host']) && isset($kafkaConfig['port'])))
		{
			KalturaLog::debug("No Kafka brokers configured message will not be sent");
			return;
		}

		if (isset($kafkaConfig['brokers']))
		{
			$this->brokers = $kafkaConfig['brokers'];
		}
		else
		{
			$this->brokers = $kafkaConfig['host'] . ":" . $kafkaConfig['port'];
		}

		$conf = new RdKafka\Conf();
		$conf->set('log_level', (string)LOG_DEBUG);
		$conf->set('metadata.broker.list', $this->brokers);

		if (isset($kafkaConfig['username']) && isset($kafkaConfig['password']))
		{
			$conf->set('sasl.username', $kafkaConfig['username']);
			$conf->set('sasl.password', $kafkaConfig['password']);
		}

		$producer = new RdKafka\Producer($conf);
		$producer->addBrokers($this->brokers);

		$this->producer = $producer;
		$this->flushTtl = isset($kafkaConfig['flushTtl']) ? $kafkaConfig['flushTtl'] : self::DEFAULT_FLUSH_TTL;
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
		$retry = 1;
		$msgSendStart = microtime(true);

		try
		{
			$this->create($topicName);
			$this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $msgArgs["partitionKey"]);
			$this->producer->poll(0);
			$result = $this->producer->flush($this->flushTtl);

			while ($this->producer->getOutQLen() > 0 && $retry <= self::MAX_RETRIES)
			{
				$this->producer->poll(1);
				$retry++;
			}
		}
		catch (Exception $e)
		{
			$this->writeToMonitor("Failed to publish message to topic name $topicName iteration $i", $this->brokers, self::KAFKA_ACTION_SEND_MESSAGE, microtime(true) - $start, $topicName, strlen($message), $e->getCode());
			throw $e;
		}

		if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result)
		{
			$this->writeToMonitor("Failed to publish message to topic name $topicName", $this->brokers, self::KAFKA_ACTION_SEND_MESSAGE, microtime(true) - $msgSendStart, $topicName, strlen($message), $result);
			KalturaLog::err("producing kafka msg failed");
		}

		$this->writeToMonitor("Msg sent to $topicName", $this->brokers, self::KAFKA_ACTION_SEND_MESSAGE, microtime(true) - $msgSendStart, $topicName, strlen($message));
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
				$result = $this->producer->flush($this->flushTtl);
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
		$this->producer->flush($this->flushTtl);
	}

	protected function writeToMonitor($logStr, $dataSource, $queryType, $queryTook, $tableName = null, $querySize = null, $errorType = '')
	{
		if (class_exists('KalturaLog')) {
			KalturaLog::debug($logStr);
		}

		if (class_exists('KalturaMonitorClient')) {
			KalturaMonitorClient::monitorKafkaAccess($dataSource, $queryType, $queryTook, $tableName, $querySize, $errorType);
		}
	}
}