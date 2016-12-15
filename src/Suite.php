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
	protected $persistent;
	protected $connection = null;

  /**
   * New Suite
   */
  public function __construct($persistent = true)
  {
    $this->protocol = new Protocol();
    $this->config   = new Config();
		$this->persistent = $persistent;

		if ($persistent) {
			$config = [];
	    if ($this->config->get('host')) {
	      $config['host'] = $this->config->get('host');
	    }

	    if ($this->config->get('port')) {
	      $config['port'] = $this->config->get('port');
	    }

	    $connection = new Connection($config);
	    $connection->connect();

			$this->connection = $connection;
		}
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

      $instance = new $classname($this->config, $this->protocol, $this->persistent, $this->connection);
			$result = [
				'name'    => ucfirst($check),
				'result'  => null,
				'message' => ''
			];

      try {
        $response = $instance->check();

				if ($response === true) {
					$result['result'] = true;
				}

				if (!$this->persistent) {
					$instance->resetConnection();
				}
      } catch(\Exception $e) {
				$result['result']  = false;
				$result['message'] = $e->getMessage();
      }

      return $result;
  }
}
