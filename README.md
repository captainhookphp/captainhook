# CaptainHook

CaptainHook is an easy to use and very flexible git hook library for php developers.
It allows you to configure your git hook actions with a simple json file.

Use CaptainHook to validate commit messages, ensure code quality or run your unit tests before you
commit or push changes to git.

You can either run cli commands, use CaptainHooks build in validators, or write
your own PHP classes that get executed by git.


[![Latest Stable Version](https://poser.pugx.org/sebastianfeldmann/captainhook/v/stable.svg?v=1)](https://packagist.org/packages/sebastianfeldmann/captainhook)
[![Downloads](https://img.shields.io/packagist/dt/sebastianfeldmann/captainhook.svg?v1)](https://packagist.org/packages/sebastianfeldmann/captainhook)
[![License](https://poser.pugx.org/sebastianfeldmann/captainhook/license.svg?v=1)](https://packagist.org/packages/sebastianfeldmann/captainhook)
[![Build Status](https://travis-ci.org/sebastianfeldmann/captainhook.svg?branch=master)](https://travis-ci.org/sebastianfeldmann/captainhook)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/badges/quality-score.png?b=master&v=1)](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/sebastianfeldmann/captainhook/?branch=master)

## Installation

Use Composer to install CaptainHook.
```bash
    $ composer require --dev sebastianfeldmann/captainhook:~1.0
```
    
After installing CaptainHook you can use the *captainhook* executable to create a configuration.
```bash
    $ vendor/bin/captainhook configure
```

Now there should be a *captainhook.json* configuration file.
To finally activate the hooks you have to install them to your local .git repository.
You can do so by running the following command.
```bash
    $ vendor/bin/captainhook install
```

## Configuration example

The *captainhook* configuration file
```json
    {
        "commit-msg": {
            "enabled": false,
            "actions": [
                {
                    "action": "\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Check\\Beams",
                    "options": []
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
