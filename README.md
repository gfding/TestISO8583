# Script for testing ISO8583 protocol

# Prerequisites
- PHP 5.6+
- [Composer](https://getcomposer.org/)

# Installation
```
mkdir -p /var/www/test-suite/
cd /var/www/test-suite
git clone https://github.com/m1ome/TestISO8583.git .
composer install
```

# Configuration
Just copy `config.json.example` to `config.json` and change **host** and **port** of DHI interface, you currently have

# Test cases
- **Balance** - Getting balance of plastic card
- **Auth** - Auth by plastic
- **Process** - Hold & Reverse in one call

# Usage examples
## Running all suites
```bash
./bin/suite
```

## Getting list or possible arguments
```bash
./bin/suite --help
```
