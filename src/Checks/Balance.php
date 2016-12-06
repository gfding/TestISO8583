<?php
namespace TestSuite\Checks;

use \TestSuite\Settings;

class Balance extends AbstractCheck
{
  public function request()
  {
    $message = $this->getPreparedMessage();
    $message->setMTI('0200');
    $message->setField(2, explode("=", Settings::VALID_TRACK2)[0]);
    $message->setField(3, Settings::PROCESSING_CODE_BALANCE);
    $message->setField(35, Settings::VALID_TRACK2);
    $message->setField(52, hex2bin(Settings::VALID_PINBLOCK));
    $message->setField(53, Settings::PINBLOCK_PARAMS);

    return $message;
  }
}
