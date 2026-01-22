<?php
function convertToUTC(string $timezone): string
{
    $dt = new DateTime(
        'now',
        new DateTimeZone($timezone)
    );

    $dt->setTimezone(new DateTimeZone('UTC'));
    return $dt->format('Y-m-d H:i:s');
}
function convertFromUTC(string $utcDateTime, string $timezone): string
{
    $dt = new DateTime($utcDateTime, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone($timezone));
    return $dt->format('Y-m-d H:i:s');
}
  function toMinutes($time) {
        [$h, $m] = explode(':', $time);
        return ((int)$h * 60) + (int)$m;
    }

    function toTime($minutes) {
        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }
    // Convert time from a given timezone to UTC
    function toUtcTime(
            string $time,          
            string $fromTimezone   
        ): string {
            $dt = new DateTime(
                '1970-01-01 ' . $time,
                new DateTimeZone($fromTimezone)
            );

            $dt->setTimezone(new DateTimeZone('UTC'));
            return $dt->format('H:i:s');
        }
    function fromUtcTime(
        string $utcTime,       
        string $toTimezone     
    ): string {
        // Use the same fixed dummy date
        $dt = new DateTime(
            '1970-01-01 ' . $utcTime,
            new DateTimeZone('UTC')
        );

        $dt->setTimezone(new DateTimeZone($toTimezone));
        return $dt->format('H:i:s');
    }
