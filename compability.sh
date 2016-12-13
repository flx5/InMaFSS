#!/bin/bash
./vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 5.3.10 --extensions=php --ignore=vendor,inc/libs .
