#!/bin/bash
source ../configurations/system.ini

pear channel-discover pear.phpunit.de
pear channel-discover components.ez.no
pear channel-discover pear.symfony-project.com
pear install phpunit/PHPUnit-3.5.7
