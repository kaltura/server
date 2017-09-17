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

	public function __construct(array $rabbitConfig, $constructorArgs)
	{
		$this->username = $rabbitConfig['username'];
		$this->password = $rabbitConfig['password'];
		$this->MQserver = $rabbitConfig['server'];
		$this->port = $rabbitConfig['port'];
		$this->curlPort = $rabbitConfig['curl_port'];
		$this->timeout = $rabbitConfig['timeout'];
		
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
		// establish connection to RabbitMQ
		$connection = new PhpAmqpLib\Connection\AMQPConnection($this->MQserver, $this->port, $this->username, $this->password);
		$channel = $connection->channel();
		
		//function assumes queue exists
		$msg = new PhpAmqpLib\Message\AMQPMessage($data, array(
			'delivery_mode' => 2
		));

		$channel->basic_publish($msg, $this->exchangeName, $queueName);

		KalturaLog::info("Message [$data] was sent to [$queueName].");
		$channel->close();
		$connection->close();
	}
}
