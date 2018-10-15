#!/bin/bash
for filename in $(find out/ -name '*.php'); do
    php -l $filename
done