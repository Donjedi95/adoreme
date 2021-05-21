<?php

namespace App;

use DateTimeZone;
use Exception;

class Utils
{
    public const MAX_DELIVERY_DATA_GENERATOR_LINES = 50000;
    public const MIN_ZIP_CODE_SIZE = 6;
    public const MAX_ZIP_CODE_SIZE = 6;
    public const NON_WORKING_DAYS = [
        6, // Saturday
        7, // Sunday
    ];
    public const DEFAULT_DATE_TIME_ZONE = 'Europe/Bucharest';

    /**
     * @throws Exception
     */
    public static function formatDate(&$date): void
    {
        if (!$date) {
            throw new Exception('Invalid date sent for formatting');
        }

        if (is_string($date)) {
            $date = new \DateTime($date, new DateTimeZone(self::DEFAULT_DATE_TIME_ZONE));
        }

        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        }
    }

    /**
     * @throws Exception
     */
    public static function initEnvData(): void
    {
        $envFileContents = file_get_contents('.env');
        if ($envFileContents === false) {
            throw new Exception('You must create and set variables in the .env file');
        }

        foreach(explode("\n", $envFileContents) as $envRow) {
            $parts = explode('=', $envRow);
            if (count($parts) === 2) {
                $_ENV[trim($parts[0])] = trim($parts[1]);
            }
        }
    }
}
