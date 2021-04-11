<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/constants.php';
require __DIR__ . '/../vendor/autoload.php';

// Ensure this environment variable is not set before executing tests.
putenv(\CaptainHook\App\Runner\Hook\PostCheckout::SKIP_POST_CHECKOUT_VAR);
