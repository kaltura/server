<?php
spl_autoload_register(function ($class) {
	
	if(strpos($class, 'PhpAmqpLib') === 0)
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		if(file_exists($path))
		{
			require_once ($path);
		}
	}
});

/**
 * @package plugins.rabbitMQ
 * @subpackage lib.enum
 */
class RabbitMQProvider extends QueueProvider
{
	private $username;
	private $password;
	private $MQserver;	 
	private $curlPort;
	private $port;
	private $timeout;
	private $exchangeName;
	private $dataSourceUrl;
	private $connectionTimeout;
	private $readWriteTimeout;
	private $channelRpcTimeout;
	
	const MAX_RETRIES = 3;

	const RABBIT_ACTION_SEND_MESSAGE = 'send_message';
	const RABBIT_ACTION_OPEN_CONNECTION = 'open_connection';
	const RABBIT_ACTION_CLOSE_CONNECTION = 'close_connection';
	const RABBIT_ACTION_OPEN_CHANNEL = 'open_channel';
	const RABBIT_ACTION_CLOSE_CHANNEL = 'close_channel';


	public function __construct(array $rabbitConfig, $constructorArgs)
	{
		$this->username = $rabbitConfig['username'];
		$this->password = $rabbitConfig['password'];
		$this->MQserver = $rabbitConfig['server'];
		$this->port = $rabbitConfig['port'];
		$this->curlPort = $rabbitConfig['curl_port'];
		$this->timeout = $rabbitConfig['timeout'];
		$this->dataSourceUrl = $this->username . ':' . $this->password . '@' . $this->MQserver . ':' . $this->port;
		$this->connectionTimeout = isset($rabbitConfig['connection_timeout']) ? $rabbitConfig['connection_timeout'] : 2;
		$this->readWriteTimeout = isset($rabbitConfig['read_write_timeout']) ? $rabbitConfig['read_write_timeout'] : 3;
		$this->channelRpcTimeout = isset($rabbitConfig['channel_rpc_timeout']) ? $rabbitConfig['channel_rpc_timeout'] : 2 ;
		
		$exchangeName = kConf::get("push_server_exchange");
		if(isset($constructorArgs['exchangeName']))
			$exchangeName = $constructorArgs['exchangeName'];
		
		$this->exchangeName = $exchangeName;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::exists()
	 */
	public function exists($queueName)
	{
		// create a new cURL resource
		$ch = curl_init();
		// set URL including username and password, and exec
		$mqURL = "http://". $this->MQserver . ":" . $this->curlPort;
		curl_setopt($ch, CURLOPT_URL,  $mqURL . "/api/queues/%2f/" . $queueName);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		$response = curl_exec($ch);
		$msgArray = json_decode($response);
		
		// close cURL resource to free up system resources
		curl_close($ch);
		// check if error has returned (error means queue doesn't exist)
		return  !(isset($msgArray->error));
	}
	
	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::create()
	 */
	public function create($queueName)
	{
		// establish connection to RabbitMQ
		$connection = new PhpAmqpLib\Connection\AMQPConnection($this->MQserver, $this->port, $this->username, $this->password, '/', false, 'AMQPLAIN', null, 'en_US', $this->connectionTimeout, $this->readWriteTimeout, null, false, 0, $this->channelRpcTimeout);
		$channel = $connection->channel();

		// durable = true to make sure that RabbitMQ will never lose our queue (if RabbitMQ server stops)
		// exclusive=false to be accessed by other connections
		// auto-delete the queue after 12hours (43200000 ms)
		$channel->queue_declare($queueName, false, true, false, false, false,
			new PhpAmqpLib\Wire\AMQPTable(array("x-expires"  => (int) $this->timeout ))
		);
		// close used resources 
		KalturaLog::info("Queue [$queueName] created.");
		$channel->close();
		$connection->close();		
	}

	/*
	 * (non-PHPdoc)
	 * @see QueueProvider::send()
	 */
	public function send($queueName, $data)
	{
		// establish connection to RabbitMQ
		for ($retry = 1; ; $retry++)
		{
			$connStart = microtime(true);
			try
			{
				$connection = new PhpAmqpLib\Connection\AMQPConnection($this->MQserver, $this->port, $this->username, $this->password, '/', false, 'AMQPLAIN', null, 'en_US', $this->connectionTimeout, $this->readWriteTimeout, null, false, 0, $this->channelRpcTimeout);
				$connTook = microtime(true) - $connStart;
				$logStr = "connected to MQserver [{$this->MQserver}]";
				$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_OPEN_CONNECTION, $connTook);
				break;
			}
			catch (Exception $e)
			{
				$connTook = microtime(true) - $connStart;
				$logStr = "Failed to connect to MQserver [{$this->MQserver}] after [$connTook] with error [" . $e->getMessage() . "]";
				$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_OPEN_CONNECTION, $connTook, $this->exchangeName . ":" . $queueName, strlen($data), $e->getCode());
				if($retry == self::MAX_RETRIES)
				{
					throw $e;
				}
			}
		}

		$channel = $this->connectChannel($connection);
		$this->publishMessage($channel, $data, $queueName);
		$this->closeChannel($channel);
		$this->closeConnection($connection);
	}

	protected function publishMessage($channel, $data, $queueName)
	{
		//function assumes queue exists
		$msg = new PhpAmqpLib\Message\AMQPMessage($data, array(
			'delivery_mode' => 2
		));

		$sendMessageStart = microtime(true);
		try
		{
			$channel->basic_publish($msg, $this->exchangeName, $queueName);
		}
		catch (Exception $e)
		{
			$sendMessageEnd = microtime(true) - $sendMessageStart;
			$logStr = "Failed to send message after [$sendMessageEnd] with data [$data] to [$queueName] with error [" . $e->getMessage() . "]";
			$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_SEND_MESSAGE, $sendMessageEnd, $this->exchangeName . ":" . $queueName, strlen($data), $e->getCode());
			throw $e;
		}

		$sendMessageEnd = microtime(true) - $sendMessageStart;
		$logStr = "Message [$data] was sent to [$queueName].";
		$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_SEND_MESSAGE, $sendMessageEnd, $this->exchangeName . ":" . $queueName, strlen($data));
	}

	protected function writeToMonitor($logStr, $dataSource, $queryType, $queryTook, $tableName = null, $querySize = null, $errorType = '')
	{
		if(class_exists('KalturaLog'))
		{
			KalturaLog::debug($logStr);
		}

		if(class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorRabbitAccess($dataSource, $queryType, $queryTook, $tableName, $querySize, $errorType);
		}
	}

	protected function connectChannel($connection)
	{
		$connStart = microtime(true);
		try
		{
			$channel = $connection->channel();
			return $channel;
		}
		catch (Exception $e)
		{
			$connTook = microtime(true) - $connStart;
			$logStr = "Connection to channel failed";
			$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_OPEN_CHANNEL, $connTook, null, null, $e->getCode());
			throw $e;
		}
	}

	protected function closeChannel($channel)
	{
		$connStart = microtime(true);
		try
		{
			$channel->close();
		}
		catch (Exception $e)
		{
			$connTook = microtime(true) - $connStart;
			$logStr = "Failed to close channel";
			$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_CLOSE_CHANNEL, $connTook, null, null, $e->getCode());
			throw $e;
		}

	}

	protected function closeConnection($connection)
	{
		$connStart = microtime(true);
		try
		{
			$connection->close();
		}
		catch (Exception $e)
		{
			$connTook = microtime(true) - $connStart;
			$logStr = "Failed to close connection";
			$this->writeToMonitor($logStr, $this->dataSourceUrl, self::RABBIT_ACTION_CLOSE_CONNECTION, $connTook, null, null, $e->getCode());
			throw $e;
		}
	}
}
