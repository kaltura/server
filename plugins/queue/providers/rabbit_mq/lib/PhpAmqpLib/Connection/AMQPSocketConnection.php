<?php
namespace PhpAmqpLib\Connection;

use PhpAmqpLib\Wire\IO\SocketIO;

class AMQPSocketConnection extends AbstractConnection
{
    /**
     * @param AbstractConnection $host
     * @param int $port
     * @param string $user
     * @param bool $password
     * @param string $vhost
     * @param bool $insist
     * @param string $login_method
     * @param null $login_response
     * @param string $locale
     * @param int $timeout
     * @param bool $keepalive
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vhost = '/',
        $insist = false,
        $login_method = 'AMQPLAIN',
        $login_response = null,
        $locale = 'en_US',
        $timeout = 3,
        $keepalive = false,
        $channel_rpc_timeout = 0.0
    ) {
        if ($channel_rpc_timeout > $timeout) {
            throw new \InvalidArgumentException('channel RPC timeout must not be greater than I/O read timeout');
        }

        $io = new SocketIO($host, $port, $timeout, $keepalive);

        parent::__construct($user, $password, $vhost, $insist, $login_method, $login_response, $locale, $io, 0, $timeout, $channel_rpc_timeout);
    }
}
