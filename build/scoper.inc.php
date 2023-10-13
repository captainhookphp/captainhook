<?php

declare(strict_types=1);

return [
    'prefix'            => 'CaptainHook\\Phar',
    'expose-namespaces' => ['CaptainHook'],
    'expose-classes' => [
        'SebastianFeldmann\Cli\Command\Runner',
        'SebastianFeldmann\Git\Repository',
    ],
];
