#!/bin/bash

set -e

scriptDir=`dirname $0`

echo -e "\n\n\n\n##### Unit Testing ####"
$scriptDir/../vendor/bin/phpunit --bootstrap vendor/autoload.php tests

echo -e "\n\n\n\n##### Linting ####"
$scriptDir/../vendor/bin/phplint

echo -e "\n\n\n\n##### Checking Formatting ####"
if $scriptDir/php-cs-fixer fix | grep -E "^ *[1-9][0-9]*) (src|test)"; then
    echo "Formatting changes found, aborting commit..."
    exit 1
fi

echo -e "\n\n\n\nSuccess."
