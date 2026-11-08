<?php 
/*********************************************************
 * Author: John Astill (c) 2002
 * Date  : 9th December 2001
 * File  : sortfunctions.php
 ********************************************************/

  function GetRawDateFromDatetime($datetime) {
    return substr($datetime,0,10);
  }

  function GetTimeFromDatetime($datetime) {
    global $Use24Hr;
    $datetime = TimeZoneOffset($datetime);
    if ($Use24Hr == "TRUE") {
      return substr($datetime,11,11);
    } else {
      return substr($datetime,11,8);
    }
  }
  
  /*************************************************************************
   * Change the time based on the configured timezone offset.
   * Datetime format = YYYY-MM-DD 
   * Show only the time not the date.
   ************************************************************************/
  function TimeOnlyZoneOffset($datetime) {
    global $timezoneOffset, $Use24Hr;
    $offs = 60*60*$timezoneOffset;
    if ($Use24Hr == "TRUE") {
      return date("h:i A",strtotime($datetime)+$offs);
    } else {
      return date("H:i",strtotime($datetime)+$offs);
    }
  }

  /*************************************************************************
   * Change the time based on the configured timezone offset.
   * Datetime format = YYYY-MM-DD 
   ************************************************************************/
  function TimeZoneOffset($datetime) {
    global $timezoneOffset, $Use24Hr;
    $offs = 60*60*$timezoneOffset;
    if ($Use24Hr == "TRUE") {
      return date("Y-m-d h:i:s A",strtotime($datetime)+$offs);
    } else {
      return date("Y-m-d H:i:s",strtotime($datetime)+$offs);
    }
  }

  /*************************************************************************
   * Some functions that are always int eh 24 hour clock. This is used 
   * for match modifications
   *************************************************************************/
  function GetTimeFromDatetime24hr($datetime) {
    $datetime = TimeZoneOffset24hr($datetime);
    return substr($datetime,11,11);
  }
  
  /*************************************************************************
   * Change the time based on the configured timezone offset.
   * Datetime format = YYYY-MM-DD 
   ************************************************************************/
  function TimeZoneOffset24hr($datetime) {
    global $timezoneOffset;
    $offs = 60*60*$timezoneOffset;
    return date("Y-m-d H:i:s",strtotime($datetime)+$offs);
  }

  /*************************************************************************
   * Change the written time based on the configured timezone offset.
   * This is used when a user enters the date, the date is converted to the
   * server date.
   * Datetime format = YYYY-MM-DD 
   ************************************************************************/
  function RevTimeZoneOffsetNo24Hour($datetime) {
    global $timezoneOffset, $Use24Hr;
    $offs = -1 *(60*60*$timezoneOffset);
    return date("Y-m-d H:i:s",strtotime($datetime)+$offs);
  }

  /*************************************************************************
   * Change the written time based on the configured timezone offset.
   * This is used when a user enters the date, the date is converted to the
   * server date.
   * Datetime format = YYYY-MM-DD 
   ************************************************************************/
  function RevTimeZoneOffset($datetime) {
    global $timezoneOffset, $Use24Hr;
    $offs = -1 *(60*60*$timezoneOffset);
    if ($Use24Hr == "TRUE") {
      return date("Y-m-d h:i:s A",strtotime($datetime)+$offs);
    } else {
      return date("Y-m-d H:i:s",strtotime($datetime)+$offs);
    }
  }

  /*************************************************************************
   * Get the screen formatted date and time from the timestamp
   * Datetime format = YYYYMMDDHHMMSS 
   ************************************************************************/
  function GetDatetimeFromTimestamp($timestamp) {
    return GetDateFromTimestamp($timestamp)." ".GetTimeFromTimestamp($timestamp);
  }

  /*************************************************************************
   * Get the screen formatted time from the timestamp
   * Datetime format = YYYYMMDDHHMMSS 
   ************************************************************************/
  function GetTimeFromTimestamp($timestamp) {
    $datetime = substr($timestamp,0,4)."-".substr($timestamp,4,2)."-".substr($timestamp,6,2)." ".substr($timestamp,8,2).":".substr($timestamp,10,2).":".substr($timestamp,12,2);
    return GetTimeFromDatetime($datetime);
  }

  /*************************************************************************
   * Get the screen formatted date from the timestamp
   * Datetime format = YYYYMMDDHHMMSS 
   ************************************************************************/
  function GetDateFromTimestamp($timestamp) {

    $datetime = substr($timestamp,0,4)."-".substr($timestamp,4,2)."-".substr($timestamp,6,2);
    return GetDateFromDatetime($datetime);
  }

  /*************************************************************************
   * Get the screen formatted date from the datetime
   * Datetime format = YYYY-MM-DD 
   ************************************************************************/
  function GetDateFromDatetime($datetime) {
    // Months is defined in the language file
    global $Months;
    
    // Allow for the TZ Offset
    $datetime = TimeZoneOffset($datetime);

    $day = substr($datetime,8,2);
    $month = substr($datetime,5,2);
    $month = $Months[$month];
    $year = substr($datetime,0,4);
    
    $date = "$day $month $year";

    return $date;
  }

  ////////////////////////////////////////////////////////////
  // Datetime format YYYY-MM-DD HH:MM:SS
  ////////////////////////////////////////////////////////////
  function convertDatetimeToScreenDate($datetime) {
    // Months is defined in the language file
    global $Months, $Use24Hr;

    $datetime = TimeZoneOffset($datetime);

    $day = substr($datetime,8,2);
    $month = substr($datetime,5,2);
    $month = $Months[$month];
    $year = substr($datetime,0,4);
    $hours = substr($datetime,11,2);
    $mins = substr($datetime,14,2);

    $date = "";
    if ($Use24Hr == "TRUE") {
      $am = substr($datetime,20,2);
      $date = "$day $month $year $hours:$mins $am";
    } else {
      $date = "$day $month $year $hours:$mins";
    }

    return $date;
  }
?>
