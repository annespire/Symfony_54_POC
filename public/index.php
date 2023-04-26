<?php

use App\Kernel;

require_once dirname(__DIR__).'/src/boxoffice/app/config.php';
require_once dirname(__DIR__).'/src/boxoffice/app/lib/includes.php';
require_once dirname(__DIR__).'/src/boxoffice/app/lib/db.php';
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
