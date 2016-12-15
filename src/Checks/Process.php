<?php
namespace TestSuite\Checks;

use \TestSuite\Settings;

class Process extends AbstractCheck
{
	const AMOUNT = 100;

  protected function request()
  {
    $message = $this->getPreparedMessage();
    $message->setMTI('0200');
    $message->setField(2, explode("=", Settings::VALID_TRACK2)[0]);
    $message->setField(3, Settings::PROCESSING_CODE_WITHDRAW);
		$message->setField(4, sprintf("%012d", self::AMOUNT));
		$message->setField(14, substr(explode("=", Settings::VALID_TRACK2)[1],0,4));
		$message->setField(19, Settings::CURRENCY_CODE);
    $message->setField(35, Settings::VALID_TRACK2);
		$message->setField(49, Settings::CURRENCY_CODE);
    $message->setField(52, hex2bin(Settings::VALID_PINBLOCK));
    $message->setField(53, Settings::PINBLOCK_PARAMS);

    return $message;
  }

	protected function expectation($response)
	{
		$holdString = $response->getField(37);

		if (!strlen($holdString)) {
			throw new \Exception('Empty 37 field in hold');
		}

		if (!is_numeric($holdString)) {
			throw new \Exception('Non-numeric 37 field in hold');
		}

		if (!$this->persistent) {
			$this->setConnection();
		}

		// Reversing this hold, to not spam a lot of shit
		$message = $this->getPreparedMessage();
		$message->setMTI('0400');
  	$message->setField(2, explode("=", Settings::VALID_TRACK2)[0]);
    $message->setField(3, Settings::PROCESSING_CODE_WITHDRAW);
    $message->setField(4, sprintf("%012d", self::AMOUNT));
    $message->setField(19, Settings::CURRENCY_CODE);
    $message->setField(35, Settings::VALID_TRACK2);
    $message->setField(37, $holdString);
    $message->setField(49, Settings::CURRENCY_CODE);

		$answer  = $this->sendMessage($message->pack());
		$reverse = $this->getEmptyMessage();
		$reverse->unpack($answer);

		$reverseString = $reverse->getField(37);
		if (!strlen($reverseString)) {
			throw new \Exception('Empty 37 field in reverse');
		}

		if (!is_numeric($reverseString)) {
			throw new \Exception('Non-numeric 37 field in reverse');
		}

		return true;
	}
}
