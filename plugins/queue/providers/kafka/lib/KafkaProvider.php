<?php

class KafkaProvider extends QueueProvider
{
	private $brokersArray;
	private $server;
	private $port;
	private $conf;
	protected $topic = null;
	protected $producer = null;
	
	const DEFAULT_PORT = 29092;
	const MAX_RETRIES = 3;
	
	public function __construct(array $kafkaConfig)
	{
		if ($kafkaConfig['brokers']) {
			$brokers = $kafkaConfig['brokers'];
			$brokersArray = explode(",", $brokers);
			$newBrokersArray = array();
			foreach ($brokersArray as &$broker) {
				$hostAndPort = explode(":", $broker);
				$host = $hostAndPort[0];
				if (!$hostAndPort[1]) {
					KalturaLog::log($host . " has no port umber, default port is defined");
					$port = self::DEFAULT_PORT;
				} else {
					$port = $hostAndPort[1];
				}
				array_push($newBrokersArray, $host . ":" . $port);
			}
			$this->brokersArray = $newBrokersArray;
		} else {
			$this->server = $kafkaConfig['server'];
			$this->port = $kafkaConfig['port'];
		}
		
		$conf = new RdKafka\Conf();
		$conf->set('log_level', (string)LOG_DEBUG);
		if ($this->server) {
			$conf->set('metadata.broker.list', $this->server . ':' . $this->port);
		}
		
		if ($kafkaConfig['username'] && $kafkaConfig['password']) {
			$conf->set('sasl.username', $kafkaConfig['username']);
			$conf->set('sasl.password', $kafkaConfig['password']);
		}
		
		$this->conf = $conf;
		$producer = new RdKafka\Producer($conf);
		if ($this->brokersArray) {
			$producer->addBrokers(implode(',', $this->brokersArray));
		}
		
		$this->producer = $producer;
		KalturaLog::log("started kafka connection: " . $this->server . ":" . $this->port);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::exists()
	 */
	public function exists($queueName)
	{
		$metaData = $this->producer->getMetadata(true, null, 1000);
		$topics = $metaData->getTopics();
		foreach ($topics as $topic) {
			if ($topic->getTopic() == $queueName)
				return true;
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
	
	/**
	 * (non-PHPdoc)
	 * @see QueueProvider::send()
	 */
	public function send($queueName, $message)
	{
		for ($retry = 1; ; $retry++) {
			try {
				$this->create($queueName);
				$this->topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode($message));
				$this->producer->poll(0);
				$result = $this->producer->flush(10000);
				break;
			} catch (Exception $e) {
				if ($retry == self::MAX_RETRIES) {
					throw $e;
				}
			}
		}
		if (!RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
			KalturaLog::err("producing kafka msg failed");
		}
		$this->producer->purge(RD_KAFKA_PURGE_F_QUEUE);
	}
	
	public function sendWithPartitionKey($queueName, $partitionKey, $kafkaPayload)
	{
		$partitionNum = $this->getTopic($queueName);
		if ($partitionNum == 0) {
			KalturaLog::err("kafka topic [$queueName] not found");
		}
		$partitionId = $this->getPartitionId($partitionKey, $partitionNum);
		
		for ($retry = 1; ; $retry++) {
			try {
				$this->create($queueName);
				$this->topic->produce($partitionId, 0, json_encode($kafkaPayload));
				$this->producer->poll(0);
				$result = $this->producer->flush(10000);
				break;
			} catch (Exception $e) {
				if ($retry == self::MAX_RETRIES) {
					throw $e;
				}
			}
		}
		if (!RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
			KalturaLog::err("producing kafka msg failed");
		}
		$this->producer->purge(RD_KAFKA_PURGE_F_QUEUE);
	}
	
	public function sendWithPartitionKeyAvro($queueName, $partitionKey, $message)
	{
		$partitionNum = $this->getTopic($queueName);
		if ($partitionNum == 0) {
			KalturaLog::err("kafka topic [$queueName] not found");
		}
		$partitionId = $this->getPartitionId($partitionKey, $partitionNum);
		
		for ($retry = 1; ; $retry++) {
			try {
				$this->create($queueName);
				$this->topic->produce($partitionId, 0, $message);
				$this->producer->poll(0);
				$result = $this->producer->flush(10000);
				break;
			} catch (Exception $e) {
				if ($retry == self::MAX_RETRIES) {
					throw $e;
				}
			}
		}
		if (!RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
			KalturaLog::err("producing kafka msg failed");
		}
		$this->producer->purge(RD_KAFKA_PURGE_F_QUEUE);
	}
	
	public function getPartitionId($partitionKey, $partitionNum)
	{
		return crc32($partitionKey) % $partitionNum;
	}
	
	public function getTopic($queueName)
	{
		$metaData = $this->producer->getMetadata(true, null, 1000);
		$topics = $metaData->getTopics();
		foreach ($topics as $topic) {
			if ($topic->getTopic() == $queueName)
				return sizeof($topic->getPartitions());
		}
		return 0;
	}
	
	public function __destruct()
	{
		$this->producer->flush(10000);
	}
	
}