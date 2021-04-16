#!/usr/bin/env php
<?php

/*
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('captainhook');

require 'phar://captainhook/vendor/symfony/polyfill-php80/bootstrap.php';

require 'phar://captainhook/.box/bin/check-requirements.php';

require 'phar://captainhook/bin/captainhook';

__HALT_COMPILER(); ?>
