# HookMeUp (alpha version)

Easy to use and very flexible lib to use git hooks for php developers.

[![Latest Stable Version](https://poser.pugx.org/sebastianfeldmann/hookmeup/v/stable.svg)](https://packagist.org/packages/sebastianfeldmann/hookmeup)
[![License](https://poser.pugx.org/sebastianfeldmann/hookmeup/license.svg)](https://packagist.org/packages/sebastianfeldmann/hookmeup)
[![Build Status](https://travis-ci.org/sebastianfeldmann/hookmeup.svg?branch=master)](https://travis-ci.org/sebastianfeldmann/hookmeup)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sebastianfeldmann/hookmeup/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sebastianfeldmann/hookmeup/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/sebastianfeldmann/hookmeup/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/sebastianfeldmann/hookmeup/?branch=master)

## Installation

Use Composer to install hookmeup.

    $ composer require --dev sebastianfeldmann/hookmeup:~1.0
    
After installing hookmeup you can use the *hookmeup* executable to create a configuration.

    $ vendor/bin/hookmeup configure
     
Now you should have a *hookmeup.json* configuration file.
To finally activate the hooks you have to install them to your local .git repository. 

    $ vendor/bin/hookmeup install

[![Install hookmeup](https://phpbu.de/images/hookmeup.gif)]
