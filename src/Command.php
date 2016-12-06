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
      'help'  => [
        'longPrefix'  => 'help',
        'description' => 'Prints out usage statement',
        'noValue'     => true
      ]
    ]);

    $climate->bold()->info('ISO8583 Testing Suite');
    $climate->dim()->info('For usage information use ./bin/suite --help');
    $climate->border();
    $climate->br();

    // Parsing args
    $climate->arguments->parse();

    $suite = $climate->arguments->get('suite');
    $help  = $climate->arguments->defined('help');

    if ($help) {
      $climate->usage();
      exit();
    }

    if ($suite === '*') {
      $climate->info('Running all suites we have right now');
      $suites = ['balance'];
    } else {
      $suites = explode(',', $suite);
      $climate->info('Running suites: ' . implode(',', $suites));
    }

    $suiteInstance = new Suite();
    foreach($suites as $suite) {
      $result = $suiteInstance->run($suite);
      var_dump($result);
    }



    // $climate->out('Printing to terminal');
  }
}
