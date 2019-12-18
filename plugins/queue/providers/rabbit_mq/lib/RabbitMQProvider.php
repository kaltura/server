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
	
	protected static $rabbitConnections = array();
	
	const MAX_RETRIES = 3;
	
	const RABBIT_ACTION_SEND_MESSAGE = 'send_message';
	const RABBIT_ACTION_OPEN_CONNECTION = 'connect';
	const RABBIT_ACTION_TIMEOUT = 'timeout';

	public function __construct(array $rabbitConfig, $constructorArgs)
	{
		$this->username = $rabbitConfig['username'];
		$this->password = $rabbitConfig['password'];
		$this->MQserver = $rabbitConfig['server'];
		$this->port = $rabbitConfig['port'];
		$this->curlPort = $rabbitConfig['curl_port'];
		$this->timeout = $rabbitConfig['timeout'];
		$this->dataSourceUrl = $this->username . ':' . $this->password . '@' . $this->MQserver . ':' . $this->port;
		$this->connectionTimeout = isset($rabbitConfig['connection_timeout']) ? $rabbitConfig['connection_timeout'] : 1;
		$this->readWriteTimeout = isset($rabbitConfig['read_write_timeout']) ? $rabbitConfig['read_write_timeout'] : 2;
		$this->channelRpcTimeout = isset($rabbitConfig['channel_rpc_timeout']) ? $rabbitConfig['channel_rpc_timeout'] : 1 ;
		
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
		$connection = new PhpAmqpLib\Connection\AMQPConnection($this->MQserver, $this->port, $this->username, $this->password);
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
		$connection = $this->getConnection();
		$channel = $connection->channel();
		
		//function assumes queue exists
		$msg = new PhpAmqpLib\Message\AMQPMessage($data, array(
			'delivery_mode' => 2
		));

		for ($retry = 1; ; $retry++)
		{
			$sendMessageStart = microtime(true);
			try
			{
				$channel->basic_publish($msg, $this->exchangeName, $queueName);
				break;
			}
			catch (PhpAmqpLib\Exception\AMQPTimeoutException $e)
			{
				if (class_exists('KalturaLog'))
				{
					KalturaLog::err("Connection timed out while sending message [$data] to [$queueName] with error [" . $e->getMessage() . "]");
				}
				if ($retry == self::MAX_RETRIES)
				{
					$sendMessageEnd = microtime(true) - $sendMessageStart;
					KalturaMonitorClient::monitorRabbitAccess($this->dataSourceUrl, self::RABBIT_ACTION_TIMEOUT, $sendMessageEnd, $this->exchangeName . ":" . $queueName, strlen($data));
					throw $e;
				}
			}
		}

		$sendMessageEnd = microtime(true) - $sendMessageStart;
		KalturaMonitorClient::monitorRabbitAccess($this->dataSourceUrl, self::RABBIT_ACTION_SEND_MESSAGE, $sendMessageEnd, $this->exchangeName . ":" . $queueName, strlen($data));

		if(class_exists('KalturaLog'))
			KalturaLog::info("Message [$data] was sent to [$queueName].");
		$channel->close();
		
		//To Do need to check when we can close the connection
		$connection->close();
	}
	
	private function getConnection()
	{
		$connection =  null;
		if(!isset(self::$rabbitConnections[$this->MQserver]))
		{
			// establish connection to RabbitMQ
			for ($retry = 1; ; $retry++)
			{
				try
				{
					$connStart = microtime(true);
					
					$connection = new PhpAmqpLib\Connection\AMQPConnection($this->MQserver, $this->port, $this->username, $this->password, '/', false, 'AMQPLAIN', null, 'en_US', $this->connectionTimeout, $this->readWriteTimeout, null, false, 0, $this->channelRpcTimeout);
					
					$connTook = microtime(true) - $connStart;
					KalturaMonitorClient::monitorRabbitAccess($this->dataSourceUrl, self::RABBIT_ACTION_OPEN_CONNECTION, $connTook);
					
					break;
				}
				catch (PhpAmqpLib\Exception\AMQPRuntimeException $e)
				{
					if(class_exists('KalturaLog'))
						KalturaLog::err("Failed to connect to MQserver [{$this->MQserver}] with error [" . $e->getMessage() . "]");
					
					if($retry == self::MAX_RETRIES)
						throw $e;
				}
			}
			
			self::$rabbitConnections[$this->MQserver] = $connection;
		}
		
		return self::$rabbitConnections[$this->MQserver] = $connection;
	}
	
	private function closeConnections()
	{
		foreach (self::$rabbitConnections as $key => $connection)
		{
			$connection->close();
		}
	}
}
