<?php
namespace TestSuite\Checks;

use \TestSuite\Settings;

class Auth extends AbstractCheck
{
  protected function request()
  {
    $message = $this->getPreparedMessage();
    $message->setMTI('0100');
    $message->setField(2, explode("=", Settings::VALID_TRACK2)[0]);
    $message->setField(3, Settings::PROCESSING_CODE_BALANCE);
		$message->setField(25, Settings::POS_CONDITION_CODE_VERIFICATION);
    $message->setField(35, Settings::VALID_TRACK2);
    $message->setField(52, hex2bin(Settings::VALID_PINBLOCK));
    $message->setField(53, Settings::PINBLOCK_PARAMS);

    return $message;
  }

	protected function expectation($response)
	{
		$authString = $response->getField(54);

		if (!strlen($authString)) {
			throw new \Exception('No 54 field in "auth" response');
		}

		return true;
	}
}
