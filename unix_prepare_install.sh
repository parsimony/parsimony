#!/bin/bash
# author : Manuel Tancoigne
# Contact on gitHub :
# copyright : -
# version 0.1
# license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
#
# This script changes the default files and folders permissions, and
# creates the "cache" folder. For Unix setups where base folder owner
# is not the server user (www-data for apache).
#
# Tested on a Linux Mint 13 Maya, runs fine
#
# TODO : interactive mode to know if we must change files/folders owner 
# or not (for people testing in their home/public_html directory)
#

echo "Creating missing folders..."
mkdir cache
echo " - Done"

echo "Changing files permisions to r/w..."
chmod 777 index.php
chmod 777 config.php
chmod 777 install.php
echo " - Done."
echo "Changing folders permissions to r/w..."
chmod 777 modules/ -R
chmod 777 profiles/ -R
chmod 777 cache/ -R
echo " - Done."
echo "Changing folders permissions to read only..."
chmod 755 lib/ -R
