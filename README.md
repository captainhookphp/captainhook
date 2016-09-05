# CaptainHook

*CaptainHook* is an easy to use and very flexible git hook library for php developers.
It enables you to configure your git hook actions in a simple json file.

You can use *CaptainHook* to validate your commit messages, ensure code quality or run unit tests before you
commit or push changes to git.

You can either run cli commands, use some build in validators, or write
your own PHP classes that get executed by *CaptainHook*. For more information have a look at the [documentation](https://sebastianfeldmann.github.io/captainhook/ "CaptainHook Documentation").


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

Have a look at this short installation video.

[![Install demo](http://img.youtube.com/vi/zOrzlYstIcY/0.jpg)](http://www.youtube.com/watch?v=zOrzlYstIcY)

## Configuration

Here's an example *captainhook.json* configuration file.
```json
{
  "commit-msg": {
    "enabled": true,
    "actions": [
      {
        "action": "\\sebastianfeldmann\\CaptainHook\\Hook\\Message\\Check\\Beams",
        "options": []
      }
    ]
  },
  "pre-commit": {
    "enabled": true,
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
