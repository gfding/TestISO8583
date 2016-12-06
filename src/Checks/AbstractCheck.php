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

  /**
   * New check constructor
   */
  public function __construct($config, $protocol)
  {
    $this->protocol = $protocol;
    $this->config   = $config;
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
    $config = [];
    if ($this->config->get('host')) {
      $config['host'] = $this->config->get('host');
    }

    if ($this->config->get('port')) {
      $config['port'] = $this->config->get('port');
    }

    $connection = new Connection($config);
    $connection->connect();

    $packedMessage = $this->request()->pack();
    $answer = $connection->write($packedMessage);
    var_dump($answer);
    // $unpackedAnswer = $this->getEmptyMessage()->unpack($answer);

    // var_dump($unpackedAnswer);
  }

  abstract function request();
  // abstract function expectation();

  // abstract public function check();
}
