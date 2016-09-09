<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('CH_PATH_FILES', realpath(__DIR__ . '/files'));

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/CaptainHook/Git/DummyRepo.php';
require __DIR__ . '/CaptainHook/Runner/BaseTestRunner.php';
