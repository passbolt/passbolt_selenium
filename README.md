passbolt selenium testsuite
===========================================

This project is the functional testsuite of Passbolt. It is based on Selenium, PhpUnit and Facebook php-webdriver

Checkout [passbolt.com](http://www.passbolt.com) for more information


## About Facebook php-webdriver

This WebDriver client is a driver developped by Facebook. It aims to be as close as possible to bindings in other languages.
The concepts are very similar to the Java, .NET, Python and Ruby bindings for WebDriver.

Looking for documentation about php-webdriver? See the official [repository](http://facebook.github.io/php-webdriver/)

##  More information

Check out the Selenium [docs and wiki](http://docs.seleniumhq.org/docs/ and https://code.google.com/p/selenium/wiki)

Learn how to integrate it with PHPUnit [Blogpost](http://codeception.com/11-12-2013/working-with-phpunit-and-selenium-webdriver.html) | [Demo Project](https://github.com/DavertMik/php-webdriver-demo)


How to get started
===========================================

##  GET THE CODE

### Github

    git clone git@github.com:passbolt/passbolt_selenium.git

### Bitbucket

    git clone git@bitbucket.org:passbolt/passbolt_selenium.git

##  CONFIG

Clone or rename config.php.default to

##  SELENIUM

*   Download the selenium-server-standalone-#.jar file provided here:

				http://selenium-release.storage.googleapis.com/index.html

*   Download and run that file, replacing # with the current server version.

        java -jar selenium-server-standalone-#.jar

*   You can also run it on a remote host, you will need to set it up as a grid/node by running two instances

        java -jar selenium-server-standalone-#.jar -role grid
        java -jar selenium-server-standalone-#.jar -role node

*   Then when you create a session, be sure to pass the url to where your server is running.

        // This would be the url of the host running the server-standalone.jar
        $host = 'http://localhost:4444/wd/hub'; // this is the default

*   Make sure you have firefox installed on your selenium host!


## PHPUNIT (Using brew or similar)

*  Get phpunit using your package manager for example

    brew phpunit

*   To run unit tests then simply run:

    phpunit -c ./tests


## PHPUNIT (Using composer)

*   If you don't want to use your local package manager (brew, apt-get, etc.) you can download the composer.phar

    curl -sS https://getcomposer.org/installer | php

*   Install the library.

    php composer.phar install

*   To run unit tests then simply run:

    ./vendor/bin/phpunit -c ./tests


## PASSBOLT Plugin

*   Get the plugin by downloading the latest build from the repository and put it in data/extensions

    https://github.com/passbolt/passbolt_ff/blob/develop/passbolt-firefox-addon.xpi?raw=true

*   Or if you are developing you can create a simlink to your addon project

		cd data/extensions
		ln -s ../../../passbolt_ff/passbolt-firefox-addon.xpi .
