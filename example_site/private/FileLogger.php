<?php

//namespace Logger;

class FileLogger
{
    private static $maxLines = 50;

    public static function recordsLogs($filename, $recordableString)
    {
        $filepath = __DIR__ . "/$filename";
        self::writeLineToFile($filepath, $recordableString);
    }

    private static function writeLineToFile($filename, $line)
    {
        $logsList = file_exists($filename) ? file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

        if (count($logsList) >= self::$maxLines) {
            array_shift($logsList);
        }

        $today = date("Y-m-d H:i:s");
        $logsList[] = "$today $line";

        file_put_contents($filename, implode(PHP_EOL, $logsList) . PHP_EOL);
    }
}
