<?php
//------------------------------------------------------------------
// package date : functions to perform operations on dates
//------------------------------------------------------------------

	class One_Script_Package_Date extends One_Script_Package
	{
		// CAUTION! Unlike in PHP, the 'is_dst' parameter (daylight savings time) defaults to 1
		function date ( $format, $timestamp )
		{
			return date ( $format, $timestamp );
		}

		function mktime( $hour, $minute, $second, $month, $day, $year, $is_dst = 1 )
		{
			return mktime($hour, $minute, $second, $month, $day, $year, $is_dst);
		}

		function datetimeFormat($format, $dt = null)
		{
			if ($dt)
				$unixtime = strtotime($dt);

			return $unixtime ? date($format, $unixtime) : date($format);
		}

		function unixtimeFormat($format, $unixtime)
		{
			return date($format, $unixtime);
		}

		function strftime ( $format, $timestamp = 0)
		{
			$dummy = setlocale(LC_ALL, 'nl_NL');

			return strftime($format, $timestamp);
		}

		function strtotime ( $time )
		{

			return strtotime($time);
		}

	}
