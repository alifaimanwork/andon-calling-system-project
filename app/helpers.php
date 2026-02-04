<?php
if (!function_exists('isSecure')) {
    function isSecure()
    {
        if (
            (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('createDateTime')) {
    function createDateTime($format, $value, $timezone = NULL, $timeStampLimit = TRUE): Datetime|false
    {


        $dt = \DateTime::createFromFormat($format, $value, $timezone);
        if (!$timeStampLimit || !$dt)
            return $dt;

        //mysql timestamp limit
        $minTimestamp = '1970-01-01 00:00:01';
        $maxTimestamp = '2038-01-19 03:14:07';

        $timestamp = $dt->format('Y-m-d H:i:s');
        if ($timestamp < $minTimestamp || $timestamp > $maxTimestamp)
            return false;

        return $dt;
    }
}

if (!function_exists('isValueRangeOverlap')) {
    function isValueRangeOverlap($aStart, $aEnd, $bStart, $bEnd): bool
    {
        if ($aStart != null && $aEnd != null && $bStart != null && $bEnd != null) {
            return (($aStart > $bStart && $aStart < $bEnd) ||
                ($aEnd > $bStart && $aEnd < $bEnd) ||
                ($bStart > $aStart && $bStart < $aEnd) ||
                ($bEnd > $aStart && $bEnd < $aEnd) ||
                ($aStart == $bStart && $aEnd == $bEnd));
        } elseif ($aStart == null && $aEnd != null && $bStart != null && $bEnd != null) {
            return (($aEnd > $bStart && $aEnd < $bEnd) ||
                ($bStart < $aEnd) ||
                ($bEnd < $aEnd));
        } elseif ($aStart != null && $aEnd == null && $bStart != null && $bEnd != null) {
            return (($aStart > $bStart && $aStart < $bEnd) ||
                ($bStart > $aStart) ||
                ($bEnd > $aStart));
        } elseif ($aStart != null && $aEnd != null && $bStart == null && $bEnd != null) {
            return (($aStart < $bEnd) ||
                ($aEnd < $bEnd) ||
                ($bEnd > $aStart && $bEnd < $aEnd));
        } elseif ($aStart != null && $aEnd != null && $bStart != null && $bEnd == null) {
            return (($aStart > $bStart) ||
                ($aEnd > $bStart) ||
                ($bStart > $aStart && $bStart < $aEnd));
        } elseif ($aStart == null && $aEnd != null && $bStart != null && $bEnd == null) {
            return (($aEnd > $bStart) || ($bStart < $aEnd));
        } elseif ($aStart != null && $aEnd == null && $bStart == null && $bEnd != null) {
            return (($aStart < $bEnd) || ($bEnd > $aStart));
        } elseif (($aStart == null && $bStart == null) || ($aEnd == null || $bEnd == null)) {
            return true;
        } elseif (($aStart == null && $aEnd == null) || ($bStart == null && $bEnd == null)) {
            return true;
        }
    }
}
if (!function_exists('isValueInsideRange')) {
    function isValueInsideRange($value, $start, $end): bool
    { //start == null for -ve Inf, end == null for +ve Inf
        if ($start != null && $end != null) {
            return $value >= $start && $value < $end;
        } elseif ($start != null && $end == null) {
            return $value >= $start;
        } elseif ($start == null && $end != null) {
            return $value < $end;
        } else {
            return true;
        }
    }
}
if (!function_exists('getOverlapDuration')) {
    function getOverlapDuration(\DateTime $aStart, \DateTime  $aEnd, \DateTime  $bStart, \DateTime  $bEnd): int
    {
        if (!isValueRangeOverlap($aStart, $aEnd, $bStart, $bEnd))
            return 0;

        $overlapStart = clone ($aStart > $bStart ? $aStart : $bStart);
        $overlapEnd = clone ($aEnd < $bEnd ? $aEnd : $bEnd);

        return $overlapEnd->getTimestamp() - $overlapStart->getTimestamp();
    }
}


if (!function_exists('minSecToTotalSeconds')) {
    function minSecToTotalSeconds($minSec)
    {
        $parts = explode(':', $minSec);
        if (count($parts) <= 0)
            return 0;
        if (count($parts) == 1)
            return intval($parts[0]);

        $min = $parts[count($parts) - 2];
        $sec = $parts[count($parts) - 1];

        return intval($min) * 60 + intval($sec);
    }
}

if (!function_exists('totalSecondsToMinSec')) {
    function totalSecondsToMinSec($totalSeconds)
    {
        $min = floor($totalSeconds / 60);
        $sec = $totalSeconds - $min * 60;

        return sprintf("%02d:%02d", $min, $sec);
    }
}

// check if path valid for read
// 0 - valid
// 1 - not valid / not exists
// 2 - not valid / not readable
// 3 - not valid / not writable
// 4 - not valid / not allowed / no permission
if (!function_exists('path_readable')) {

    function path_readable($path)
    {
        $current_path = dirname(__FILE__);


        if (!file_exists($path))
            return 1;
        if (!is_readable($path))
            return 2;

        return 0;
    }
}
//TODO: validate path
// check if path valid for write
if (!function_exists('path_writable')) {
    function path_writable($path)
    {
        $current_path = dirname(__FILE__);

        if (!file_exists($path))
            return 1;
        if (!is_writable($path))
            return 3;
        return 0;
    }
}
