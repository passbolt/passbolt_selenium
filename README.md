## ARCHIVED

This repository is archived and the code will not be updated.
Use at your own risk. 


passbolt selenium testsuite
===========================================

This project is the functional testsuite of Passbolt. It is based on Selenium, PhpUnit and Facebook php-webdriver

Checkout [passbolt.com](http://www.passbolt.com) for more information


License
==============

This project is distributed under [Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html)


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

    java -jar selenium-server-standalone-#.jar -role hub
    java -jar selenium-server-standalone-#.jar -role node

*   Then when you create a session, be sure to pass the url to where your server is running.

    // This would be the url of the host running the server-standalone.jar
    $host = 'http://localhost:4444/wd/hub'; // this is the default

*   Make sure you have firefox installed on your selenium host!


## PHPUNIT (Using brew or similar)

*  Get phpunit using your package manager for example

    brew phpunit

*   To run unit tests then simply run:

    phpunit -c ./tests/USERROLE

    where User role is
    - AN : user without plugin
    - AP : user with plugin
    - LU : user with configured plugin and user account

* You can also run all tests

    $ ./run_all_test.sh

* You can also run the tests matching a given pattern (matching class or function name), for example:

    $ phpunit -c ./tests/LU --filter PasswordDeleteTest


## PHPUNIT (Using composer)

*   If you don't want to use your local package manager (brew, apt-get, etc.) you can download the composer.phar

    curl -sS https://getcomposer.org/installer | php

*   Install the library.

    php composer.phar install

*   To run unit tests then simply run:

    ./vendor/bin/phpunit -c ./tests

## TEST WITH FIREFOX

*   Below is an example of a selenium test using firefox with the passbolt plugin, running for AP, and for the test testLogin
    PLEASE NOTE that a shortcut to the .xpi firefox plugin should be placed in data/extensions folder

    BROWSER=firefox_with_passbolt_extension ./passbolt/vendor/phpunit/phpunit/phpunit -c ./tests/AP/phpunit.xml --filter testLogin

## TEST WITH CHROME

*   Below is an example of a selenium test using chrome with the passbolt plugin, running for AP, and for the test testLogin.
    PLEASE NOTE that a shortcut to the .crx chrome plugin should be placed in data/extensions folder

    BROWSER=chrome_with_passbolt_extension ./passbolt/vendor/phpunit/phpunit/phpunit -c ./tests/AP/phpunit.xml --filter testLogin


## PASSBOLT Plugin

*   Get the plugin by downloading the latest build from the repository and put it in data/extensions

    https://github.com/passbolt/passbolt_ff/blob/develop/passbolt-firefox-addon.zip?raw=true

*   Or if you are developing you can create a simlink to your addon project

		cd data/extensions
		ln -s ../../../passbolt_ff/passbolt-firefox-addon.zip .

## PASSBOLT Fixtures

*   Place the GPG keys in data/fixtures/gpg from

https://github.com/passbolt/passbolt/tree/develop/app/Config/gpg

*   Or if you are developing you can create a simlink to your passbolt project

    cd data/fixtures
    ln -s ../../../passbolt/app/Config/gpg/ .


About Facebook php-webdriver
===========================================

This WebDriver client is a driver developped by Facebook. It aims to be as close as possible to bindings in other languages.
The concepts are very similar to the Java, .NET, Python and Ruby bindings for WebDriver.

Looking for documentation about php-webdriver?
- [API](http://facebook.github.io/php-webdriver/)
- [Repository](https://github.com/facebook/php-webdriver)


##  More information

Check out the Selenium [docs and wiki](http://docs.seleniumhq.org/docs/ and https://code.google.com/p/selenium/wiki)

Learn how to integrate it with PHPUnit [Blogpost](http://codeception.com/11-12-2013/working-with-phpunit-and-selenium-webdriver.html) | [Demo Project](https://github.com/DavertMik/php-webdriver-demo)
