#!/usr/bin/env bash

PASSBOLT_EXTENSION_DIR='/var/www/passbolt_firefox'
PASSBOLT_SELENIUM_DIR='/var/www/passbolt_selenium'

FIREFOX_EXTENSION=`ls -v $PASSBOLT_EXTENSION_DIR/dist/firefox/passbolt-*-debug.zip`
CHROME_EXTENSION=`ls -v $PASSBOLT_EXTENSION_DIR/dist/chrome/passbolt-*-debug.crx`

ln -sf $FIREFOX_EXTENSION $PASSBOLT_SELENIUM_DIR/data/extensions/passbolt-firefox-addon.zip
ln -sf $CHROME_EXTENSION $PASSBOLT_SELENIUM_DIR/data/extensions/passbolt-chrome-addon.crx