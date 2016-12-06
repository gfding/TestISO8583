<?php
namespace TestSuite;

use ISO8583\Protocol;

/**
 * Suite
 */
class Suite
{
  protected $config;
  protected $suite;

  /**
   * New Suite
   */
  public function __construct()
  {
    $this->protocol = new Protocol();
    $this->config   = new Config();
  }

  /**
   * Running a check based on a check name
   *
   * @param  string $check Check name, in e.g. for balance checking it should be: balance
   *
   * @throws \TypeError
   */
  public function run($check)
  {
      $classname = '\\TestSuite\\Checks\\' . ucfirst($check);

      if (!class_exists($classname)) {
        throw new \TypeError('Unknown check ' . ucfirst($check) . ' please read documentation');
      }

      $instance = new $classname($this->config, $this->protocol);
      $instance->check();
  }
}
