<?php
namespace TestSuite;

/**
 * Config
 */
class Config
{
  protected $config;

  /**
   * Read configuration file, parse it from JSON and loads into class
   *
   * @throws \Exception
   */
  public function __construct()
  {
    $configFile = dirname(__DIR__) . '/config.json';
    if (!file_exists($configFile)) {
      throw new \Exception('Please create config.json in root directory');
    }

    $config = json_decode(file_get_contents($configFile), true);
    if (count($config) == 0) {
      throw new \Exception('Looks like you have empty config or not JSON one');
    }

    foreach($config as $key=>$val) {
      $this->set($key, $val);
    }
  }

  /**
   * Getting configuration value.
   *
   * @param  string $name Name of configuration attribute/value
   * @return string|integer|float|boolean|array
   */
  public function get($name)
  {
    return $this->config[$name] ?: null;
  }

  /**
   * Sets configuration value.
   *
   * @param string $name  Name of value
   * @param string|boolean|integer|float|array $value Value to set
   */
  public function set($name, $value)
  {
    $this->config[$name] = $value;
  }
}
