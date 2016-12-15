<?php
namespace TestSuite\Checks;

use ISO8583\Message;
use TestSuite\Settings;
use TestSuite\Connection;

/**
 * AbstractCheck
 */
abstract class AbstractCheck
{
  protected $protocol;
  protected $config;
  protected $request;
  protected $expectation;
	protected $connection;
	protected $persistent;

  /**
   * New check constructor
   */
  public function __construct($config, $protocol, $persistent = true, $connection = null)
  {
    $this->protocol = $protocol;
    $this->config   = $config;
		$this->persistent = $persistent;

		if ($connection === null) {
			$this->setConnection();
		} else {
			$this->connection = $connection;
		}
  }

  /**
   * Returns empty ISO8583 Message
   *
   * @return \ISO8583\Message ISO8583 Message object
   */
  protected function getEmptyMessage()
  {
    return new Message($this->protocol, [
      'lengthPrefix' => 5
    ]);
  }

  /**
   * Get fully prepared ISO8583 Message.
   *
   * @return \ISO8583\Message
   */
  protected function getPreparedMessage()
  {
    $message = $this->getEmptyMessage();

    $message->setField(4, "000000000000");
    $message->setField(7, date('mdHis'));
    $message->setField(11, substr($this->generateRequestId(), -6));
    $message->setField(18, Settings::MERCHANT_TYPE);
    $message->setField(19, Settings::CURRENCY_CODE);
    $message->setField(22, Settings::POS_ENTRY_MODE_MAGNETIC);
    $message->setField(25, Settings::POS_CONDITION_CODE_NORMAL);
    $message->setField(32, Settings::ACQUIRING_INSTITUTION_ID);
    $message->setField(37, substr(date('yz'), 1) . substr($this->generateRequestId(), -8));
    $message->setField(41, Settings::TERMINAL_ID);
    $message->setField(42, Settings::ACCEPTOR_ID);
    $message->setField(49, Settings::CURRENCY_CODE);
    $message->setField(60, Settings::ADDITIONAL_POS_INFO);

    return $message;
  }

  /**
   * Return random request ID
   *
   * @return integer
   */
  protected function generateRequestId()
  {
    return rand(10000000, 99999999);
  }

  public function check()
  {
		$message = $this->request()->pack();
		$answer = $this->sendMessage($message);
    $unpackedAnswer = $this->getEmptyMessage();
		$unpackedAnswer->unpack($answer);

		return $this->expectation($unpackedAnswer);
  }

	public function setConnection()
	{
		$config = [];
    if ($this->config->get('host')) {
      $config['host'] = $this->config->get('host');
    }

    if ($this->config->get('port')) {
      $config['port'] = $this->config->get('port');
    }

		$attempts   = 0;
		$lastError  = null;
		$errors     = [];
    
		while($attempts < 10) {
			try {
				$connection = new Connection($config);
	    	$connection->connect();
				break;
			} catch(\Exception $e) {
				sleep(1);
				$attempts++;
				$lastError = $e->getMessage();
				$errors[] = $e->getMessage();
			}
		}

		var_dump($errors);

		if ($attempts === 10) {
			throw new \Exception($lastError);
		}

		$this->connection = $connection;
	}

	public function resetConnection()
	{
		$this->connection->disconnect();
		$this->connection = null;
	}

	protected function sendMessage($message)
	{
    return $this->connection->write($message);
	}

  abstract protected function request();
  abstract protected function expectation($response);
}
