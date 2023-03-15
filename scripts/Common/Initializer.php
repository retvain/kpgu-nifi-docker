<?php

declare(strict_types=1);

namespace Common;

use Libs\Config;
use Voskhod\TechExtensions\Enums\FileNameEnums;

class Initializer
{
    /**
     * Прочитать и инициализировать настройки приложения
     *
     * @param string $logsFolder
     * @return void
     */
    public static function initSettings(string $logsFolder): void
    {
        $envFilepath = str_ireplace(DIRECTORY_SEPARATOR . 'Common', '', __DIR__)
            . DIRECTORY_SEPARATOR
            . FileNameEnums::ENV;

        $settings = parse_ini_file($envFilepath);

        if (count($settings) > 0) {
            foreach ($settings as $key => $setting) {
                Config::set($key, $setting);
            }
        }

        // настройка логирования
        Config::set('logs_dir', $logsFolder);
        Config::set('log_file_bytes_limit', 1 * 1024 * 1024);
        Config::set('log_file_count_limit', 10);

        // настройки для работы с БД через библиотеку xpackages
        Config::set('db_main.host', $settings['DB_HOST'] ?? null);
        Config::set('db_main.user', $settings['DB_USERNAME'] ?? null);
        Config::set('db_main.password', $settings['DB_PASSWORD'] ?? null);
        Config::set('db_main.db_name', $settings['DB_NAME'] ?? null);

        // настройка для получения папки storage
        Config::set('storage', ROOT_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
    }
}