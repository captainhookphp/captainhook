[![Latest Stable Version](https://poser.pugx.org/captainhook/captainhook/v/stable.svg?v=1)](https://packagist.org/packages/captainhook/captainhook)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)
[![Downloads](https://img.shields.io/packagist/dt/captainhook/captainhook.svg?v1)](https://packagist.org/packages/captainhook/captainhook)
[![License](https://poser.pugx.org/captainhook/captainhook/license.svg?v=1)](https://packagist.org/packages/captainhook/captainhook)
[![Build Status](https://github.com/captainhookphp/captainhook/workflows/Continuous%20Integration/badge.svg)](https://github.com/captainhookphp/captainhook/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/captainhookphp/captainhook/badges/quality-score.png?b=master&v=1)](https://scrutinizer-ci.com/g/captainhookphp/captainhook/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/captainhookphp/captainhook/badges/coverage.png?b=master&v=1)](https://scrutinizer-ci.com/g/captainhookphp/captainhook/?branch=master)
[![Twitter](https://img.shields.io/badge/Twitter-%40captainhookphp-blue.svg)](https://twitter.com/intent/user?screen_name=captainhookphp)

# CaptainHook

<img src="https://captainhookphp.github.io/captainhook/gfx/ch.png" alt="CaptainHook logo" align="right" width="200"/>

*CaptainHook* is an easy to use and very flexible git hook library for php developers.
It enables you to configure your git hook actions in a simple json file.

You can use *CaptainHook* to validate or prepare your commit messages, ensure code quality
or run unit tests before you commit or push changes to git. You can automatically clear
local caches or install the latest composer dependencies after pulling the latest changes.

*CaptainHook* makes it easy to share hooks within your team and even can make sure that
everybody in your team activates the hooks locally.

You can run cli commands, use some built in validators, or write
your own PHP classes that get executed by *CaptainHook*.
For more information have a look at the [documentation](https://captainhookphp.github.io/captainhook/ "CaptainHook Documentation").

## Installation

Install the *CaptainHook* PHAR using [Phive](https://phar.io/) or download the PHAR from the github [release page](https://github.com/captainhookphp/captainhook/releases/latest).
```bash
phive install captainhook
```

Or use *Composer* to install *CaptainHook*.
```bash
composer require --dev captainhook/captainhook
```

## Setup
After installing CaptainHook you can use the *captainhook* executable to create a configuration.
```bash
vendor/bin/captainhook configure
```
Now there should be a *captainhook.json* configuration file.

If you are not using the `composer-plugin` yet you have to activate the hooks manually by installing them to
your local .git repository. To do so just run the following *captainhook* command.
```bash
vendor/bin/captainhook install
```

Have a look at this short installation video.

[![Install demo](http://img.youtube.com/vi/agwTZ0jhDDs/0.jpg)](http://www.youtube.com/watch?v=agwTZ0jhDDs)

If you want to make sure your whole team uses the same hooks and you want to make sure everybody has the
hooks installed you can use the *CaptainHook* `composer-plugin` as an addition.
```bash
composer require --dev captainhook/plugin-composer
```
The plugin will make sure that the hooks get activated after every `composer install` or `update`.
If you don't like the extra dependency just add the following `scripts` command to your `composer.json` file instead.

```json
{
  "scripts": {
    "post-autoload-dump": "vendor/bin/captainhook install -f -s"
  }
}
```

## Configuration

Here's an example *captainhook.json* configuration file.
```json
{
  "commit-msg": {
    "enabled": true,
    "actions": [
      {
        "action": "\\CaptainHook\\App\\Hook\\Message\\Action\\Beams",
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

## Contributing

So you'd like to contribute to the `CaptainHook` library? Excellent! Thank you very much.
I can absolutely use your help.

Have a look at the [contribution guidelines](CONTRIBUTING.md).
