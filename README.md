# CaptainHook (alpha version)

Easy to use and very flexible git hook lib for php developers.

[![Latest Stable Version](https://poser.pugx.org/sebastianfeldmann/captainhook/v/stable.svg?v=1)](https://packagist.org/packages/sebastianfeldmann/captainhook)
[![License](https://poser.pugx.org/sebastianfeldmann/captainhook/license.svg?v=1)](https://packagist.org/packages/sebastianfeldmann/captainhook)
[![Build Status](https://travis-ci.org/sebastianfeldmann/captainhook.svg?branch=master)](https://travis-ci.org/sebastianfeldmann/captainhook)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/badges/quality-score.png?b=master&v=1)](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/?branch=master)

## Installation

Use Composer to install captainhook.

    $ composer require --dev sebastianfeldmann/captainhook:~1.0
    
After installing captainhook you can use the *captainhook* executable to create a configuration.

    $ vendor/bin/captainhook configure
     
Now you should have a *captainhook.json* configuration file.
To finally activate the hooks you have to install them to your local .git repository. 

    $ vendor/bin/captainhook install

## Configuration

The *captainhook* configuration file
```json
    {
        "commit-msg": {
            "enabled": false,
            "actions": [
                {
                    "action": "\\CaptainHook\\App\\Hook\\Message\\Beams",
                    "options": {}
                }
            ]
        },
        "pre-commit": {
            "enabled": false,
            "actions": [
                {
                    "action": "phpunit"
                },
                {
                    "action": "phpcs --standard=psr2 src"
                }
            ]
        },
        "pre-push": {
            "enabled": false,
            "actions": []
        }
    }
```
