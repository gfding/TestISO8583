<?php
namespace TestSuite;

use \League\CLImate\CLImate;

/**
 * Command
 */
class Command
{

  public static function run()
  {
    $climate = new CLImate();
    $climate->description('ISO8583 Testing Suite');

    // Defining arguments
    $climate->arguments->add([
      'suite' => [
        'prefix'        => 's',
        'longPrefix'    => 'suite',
        'description'   => 'Suites, defined by comma separated strings (Default: *)',
        'defaultValue'  => '*'
      ],
			'persistent' => [
				'prefix'				=> 'p',
				'logPrefix'			=> 'persistent',
				'description'		=> 'Use only one connection, persistent',
				'castTo'				=> 'bool',
				'defaultValue'	=> true
			],
      'help'  => [
        'longPrefix'  => 'help',
        'description' => 'Prints out usage statement',
        'noValue'     => true
      ]
    ]);

    $climate->bold()->info('ISO8583 Testing Suite');
    $climate->dim()->info('For usage information use ./bin/suite --help');
    $climate->draw('bender');
    $climate->border();
    $climate->br();

    // Parsing args
    $climate->arguments->parse();

    $suite = $climate->arguments->get('suite');
    $help  = $climate->arguments->defined('help');
		$persistent = $climate->arguments->get('persistent');
		var_dump($persistent);

    if ($help) {
      $climate->usage();
      exit();
    }

    if ($suite === '*') {
      $climate->info('Running all suites we have right now');
      $suites = [
				'balance',
				'auth',
				'process',
			];
    } else {
      $suites = explode(',', $suite);
      $climate->info('Running suites: ' . implode(',', $suites));
    }

		$climate->info('Connection state: ' . ($persistent ? 'PERSISTENT' : 'ONETIME'));

    $resultsCli = $climate->padding(30);
    $suiteInstance = new Suite($persistent);
    foreach($suites as $suite) {
      $result = $suiteInstance->run($suite);

      $name = '<bold>' . $result['name'] . '</bold>';
      $res  = '<bold>' . ($result['result'] ? '<blue>Passed</blue>' : '<red>Failed</red>' ) . '</bold>';
			if (strlen($result['message'])) {
      	$res .= '(' . $result['message'] . ')';
			}

      $resultsCli->label($name)->result($res);
    }
  }
}
