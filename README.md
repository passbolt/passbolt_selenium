	      ____                  __          ____
	     / __ \____  _____ ____/ /_  ____  / / /_
	    / /_/ / __ `/ ___/ ___/ __ \/ __ \/ / __/
	   / ____/ /_/ (__  |__  ) /_/ / /_/ / / /_
	  /_/    \__,_/____/____/_.___/\____/_/\__/

	The open source password manager for teams
	(c) 2022 Passbolt SA


License
==============

Passbolt is distributed under [Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html)

Images and logos in /src/img/third_party belongs to their respective owner.


About
=========

This is the official E2E testing styleguide for Passbolt the open source password manager for teams.
This styleguide is dedicated to developers who want to improve Passbolt and aim at enriching their
contribution with E2E tests.

Credits
=========

https://www.passbolt.com/credits

Prerequisite
============

You need to have a passbolt environment build locally with selenium activate in config file
and have done a sql export of the dummy data.

Install
=========

### Running locally

Before running the E2E tests, be sure you have Chrome and Firefox Nightly as installed.

You have to build a local version of Passbolt browser extension for both browsers. To do this, first you need to
checkout the Passbolt browser extension [repository](https://github.com/passbolt/passbolt_browser_extension) and go
to the passbolt-browser-extension folder.

For Chrome, run the command:

```shell
grunt build-chrome-debug
```

For firefox, run the command

```shell
grunt build-firefox-debug
```

That is going to generate a folder file and a crx file respectively in dist/firefox/passbolt-latest and
dist/chrome/passbolt-latest@passbolt.com.crx.

The Webdriver configuration file uses variable environment PASSBOLT_BROWSER_EXTENSION_CHROME and PASSBOLT_BROWSER_EXTENSION_FIREFOX
and PASSBOLT_BROWSER_BINARY_FIREFOX to specify the location of these files. So, they should properly defined into your environment such as
in an Unix-based platform:

```shell
export PASSBOLT_BROWSER_EXTENSION_CHROME=<YOUR-CHROME-CRX-FILE-PATH>
export PASSBOLT_BROWSER_EXTENSION_FIREFOX=<YOUR-FIREFOX-EXTENSION-FOLDER-PATH>
export PASSBOLT_BROWSER_BINARY_FIREFOX=<YOUR-FIREFOX-NIGHTLY-BINARY-FILE-PATH>
export BASE_URL_PRO=<YOUR-BASE-URL-PRO-EDITION>
export BASE_URL_CE=<YOUR-BASE-URL-CE-EDITION>
```
Be aware that Chrome is expecting a path for a `.crx` file whereas Firefox expects a folder path (such as the `build/all` folder generated with the previous commands).

Finally, run the test as follows for 'pro' or 'ce' edition :

```shell
npx wdio wdio.local.pro.conf.js
npx wdio wdio.local.ce.conf.js
```
### Running saucelabs

You have to build a local version of Passbolt browser extension for both browsers. To do this, first you need to
checkout the Passbolt browser extension [repository](https://github.com/passbolt/passbolt_browser_extension) and go
to the passbolt-browser-extension folder.

For Chrome, run the command:

```shell
grunt build-chrome-debug
```

For firefox, run the command

```shell
grunt build-firefox-debug
```

That is going to generate a folder file and a crx file respectively in dist/firefox/passbolt-latest and
dist/chrome/passbolt-latest@passbolt.com.crx.

The Webdriver configuration file uses variable environment PASSBOLT_BROWSER_EXTENSION_CHROME and PASSBOLT_BROWSER_EXTENSION_FIREFOX
to specify the location of these files.

You need also to have SAUCELABS_USERNAME and SAUCELABS_ACCESS_KEY. Pay attention to have a 36 characters long for the access key,
if it's not the case generate a new one on sauce labs.

So, they should properly defined into your environment such as
in an Unix-based platform:

```shell
export PASSBOLT_BROWSER_EXTENSION_CHROME=<YOUR-CHROME-CRX-FILE-PATH>
export PASSBOLT_BROWSER_EXTENSION_FIREFOX=<YOUR-FIREFOX-EXTENSION-FOLDER-PATH>
export BASE_URL_PRO=<YOUR-BASE-URL-PRO-EDITION>
export BASE_URL_CE=<YOUR-BASE-URL-CE-EDITION>
export SAUCELABS_USERNAME=<YOUR-SAUCELABS-USERNAME>
export SAUCELABS_ACCESS_KEY=<YOUR-SAUCELABS-ACCESS-KEY>
```
Be aware that Chrome is expecting a path for a `.crx` file whereas Firefox expects a folder path (such as the `build/all` folder generated with the previous commands).

Finally, run the test as follows for 'pro' or 'ce' edition :

```shell
npx wdio wdio.saucelabs.pro.conf.js
npx wdio wdio.saucelabs.ce.conf.js
```

### Running with shell

Before execute it, check if the prerequisite is done.
You could execute the run_selenium_test.sh and choose which version launch.
If some environment variable are not set, the script will ask you and set it.

```shell
./bin/run_selenium_tests.sh
```

Setup base data
=========

In order to run properly, these tests are assuming that a server is running with a database having a set of data already available.
Passbolt provides the required data. To set them up, you need to access your server via a command line.
Then go to the root folder of passbolt application (i.e. it could be something like `/var/www/passbolt`).

In that folder you can run the following commands:

```shell
./bin/cake passbolt insert default
./bin/cake passbolt cleanup
./bin/cake passbolt mysql_export --file selenium_tests.sql
```

The last command might ease your process later in case of a test that fails. It actually dumps your database into the specified `.sql` file.

Normally, when the tests are run, the data should get back to their initiale state. However, if an error occurs chances are that the data
will be corrupted. To recover them, you can simply use the previously generated SQL file for an import.

```shell
./bin/cake passbolt mysql_import --file selenium_tests.sql
```
