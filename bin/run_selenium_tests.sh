#!/bin/bash
# Bash menu script to test pro or ce version in local or with saucelabs

# If the variable is not set ask and set it
isVariableSet () {
  if [ -z "${!1}" ];
    then read -p "$2 " path;
    export $1=$path
  fi
}

# Choose the version to launch
PS3='Please enter your choice: '
options=("Local PRO Version" "Local CE Version" "SauceLabs PRO Version" "SauceLabs CE Version" "Quit")
select opt in "${options[@]}"
do
    case $opt in
        "Local PRO Version")
            isVariableSet PASSBOLT_BROWSER_EXTENSION_CHROME "Enter the path to the chrome browser extension:"
            isVariableSet PASSBOLT_BROWSER_EXTENSION_FIREFOX "Enter the path to the firefox browser extension:"
            npx wdio wdio.local.pro.conf.js
            break
            ;;
        "Local CE Version")
            isVariableSet PASSBOLT_BROWSER_EXTENSION_CHROME "Enter the path to the chrome browser extension:"
            isVariableSet PASSBOLT_BROWSER_EXTENSION_FIREFOX "Enter the path to the firefox browser extension:"
            npx wdio wdio.local.ce.conf.js
            break
            ;;
        "SauceLabs PRO Version")
            isVariableSet SAUCELABS_USERNAME "Enter your saucelabs username:"
            isVariableSet SAUCELABS_ACCESS_KEY "Enter your saucelabs access key:"
            npx wdio wdio.saucelabs.pro.conf.js
            break
            ;;
        "SauceLabs CE Version")
            isVariableSet SAUCELABS_USERNAME "Enter your saucelabs username:"
            isVariableSet SAUCELABS_ACCESS_KEY "Enter your saucelabs access key:"
            npx wdio wdio.saucelabs.ce.conf.js
            break
            ;;
        "Quit")
            break
            ;;
        *) echo "invalid option $REPLY";;
    esac
done