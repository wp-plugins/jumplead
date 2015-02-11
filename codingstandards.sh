#!/bin/bash

echo ----------------------------------------------------------------------
echo Checking: PHPCompatibility
echo ----------------------------------------------------------------------
./vendor/bin/phpcs --standard=PHPCompatibility --ignore=vendor --runtime-set testVersion 5.3 ./


echo ----------------------------------------------------------------------
echo Checking: WordPress
echo ----------------------------------------------------------------------
phpcbf --standard=WordPress --ignore=vendor ./ >> /dev/null;
phpcs -n --standard=WordPress --ignore=vendor ./;

echo ----------------------------------------------------------------------
echo Checking: Short Array Syntax
echo ----------------------------------------------------------------------
pcregrep -rnM "=[ \t\r\n]*\[" *.php Jumplead views


echo
echo