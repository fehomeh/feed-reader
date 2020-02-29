<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}
passthru(
    sprintf(
        'APP_ENV=test php "%s/../bin/console" doctrine:schema:drop --force',
        __DIR__
    )
);

passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:query:sql "DROP TABLE IF EXISTS migration_versions" -n --env=test',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:migrations:migrate -n --env=test',
        __DIR__
    )
);
