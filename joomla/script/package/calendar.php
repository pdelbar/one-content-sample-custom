<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

	class One_Script_Package_Calendar extends One_Script_Package
	{
		function formatCalendarArray($model, $att, $from, $to)
		{
			// prepare data
			$ndays = cal_days_in_month(CAL_GREGORIAN, date($from, 'm'), date($from, 'Y'));

			$result = array();
			for ($i=1; $i <= $ndays ; $i++) $result[$i] = array();

			foreach ($model as $item) {
	//			$item->__dumpData();
				$d = date_parse($item->$att);
//				echo "<br />new day:";
//				print_r($d);
				$result[ $d['day'] ][] = $item;
			}
//			die;
			return $result;
		}
	}
