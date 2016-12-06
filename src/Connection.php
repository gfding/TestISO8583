<?php
namespace TestSuite;

use \TestSuite\Exceptions\SocketError;

/**
 * Connect
 */
class Connection
{
	const READ_SIZE = 1024;

	protected $socket = null;
	protected $options;

	/**
	 * Create new object of connection socket.
	 * Options may be:
	 * - host (default: 127.0.0.1)
	 * - port (default: 3000)
	 *
	 * @param array $options Options for socket connection
	 */
	public function __construct($options)
	{
		$defaults = [
			'host' 	=> '127.0.0.1',
			'port'	=> '3000'
		];

		$this->options 	= $options + $defaults;
		$this->socket 	= socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	}

	/**
	 * Establish socket Connection
	 *
	 * @return bool
	 * @throws Exceptions\SocketError
	 */
	public function connect()
	{
		if (@socket_connect($this->socket, $this->options['host'], $this->options['port'])) {
			socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, [
				'sec'	=> 5,
				'usec'	=> 0
			]);

			return true;
		}

		throw new SocketError('Socket connection error: ' . socket_last_error($this->socket));
	}

	/**
	 * Close socket
	 *
	 * @return void
	 */
	public function disconnect()
	{
		socket_close($this->socket);
		$this->socket = null;
	}

	/**
	 * Write data to socket and read full answer.
	 *
	 * @param  string $data Hexed data string
	 *
	 * @throws TypeError
	 * @return string       Answer returned from socket
	 */
	public function write($data)
	{
		if (!ctype_xdigit($data)) {
			throw new \TypeError('$data should be a hex string');
		}

		if ($this->socket === null) {
			$this->connect();
		}

		// Sending data to socket
		$binaryData = hex2bin($data);
		socket_write($this->socket, $binaryData);

		// Reading all incoming data (answer)
		$s      = $this->socket;
		$buffer = socket_read($s, self::READ_SIZE);

		if (!$buffer) {
			throw new SocketError('Empty response from socket');
		}

		$sockets = [$s];
		while(socket_select($sockets, $w, $e, 0)) {
			$buffer .= socket_read($s, self::READ_SIZE);
			$sockets = [$s];
		}

		$buffer = bin2hex($buffer);

		return $buffer;
	}
}
