<?php

declare(strict_types=1);

use Common\Initializer;
use Libs\DB;
use Libs\Log;

require_once '../vendor/autoload.php';

const ROOT_DIR = __DIR__;

$logsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;

Initializer::initSettings($logsFolder);

$refreshNeeded = $argv[1] ?? null;

DB::connect();

$migrationsFolderPath = __DIR__ . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR;
$cur_ver = 0;

if ($refreshNeeded === 'refresh') {
    $dbName = \Libs\Config::get('db_main.db_name');
    Log::debugAndEcho("Deleting all tables...");
    $sql_content = <<<SQL
drop schema if exists public cascade;
SQL;

    DB::$connection->exec($sql_content);
    DB::$connection->exec("CREATE SCHEMA IF NOT EXISTS public");
    Log::debugAndEcho("All tables has ben deleted successfully.");
}

while (file_exists($migrationsFolderPath . "migrate_" . ($cur_ver + 1) . ".sql")) {

    Log::debugAndEcho("Executing migration #" . ($cur_ver + 1));

    $migration_file_path = $migrationsFolderPath . "migrate_" . ($cur_ver + 1) . ".sql";

    $sql_content = file_get_contents($migration_file_path);

    //$sql_content .= "\r\n\r\n".'UPDATE public."sql.migrations" SET cur_ver='. ($cur_ver+1) .';'."\r\n";

    DB::$connection->exec($sql_content);

    Log::debugAndEcho('Migration #' . ($cur_ver + 1) . ' done.');

    $cur_ver++;

}

