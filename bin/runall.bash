#!/bin/bash
#
# Run this script from the root directory
# This will run the code sniffer and unit tests
#

php bin/phpcs --standard=PSR2 src/

echo '----------------------------------------------------------------------'
php bin/phpunit --bootstrap vendor/autoload.php tests/

