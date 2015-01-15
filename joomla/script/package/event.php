<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

	class One_Script_Package_Event extends One_Script_Package
	{
		public function activeDays( $month, $year )
		{
			$q =& OneRepository::selectQuery( 'event' );
			$q->where( 'event_date', 'ge', date( "Y-m-d G:i:s", mktime( 0, 0, 0, $month, 1, $year ) ) );
			$month++;
			if( $month > 12 )
			{
				$month = 1;
				$year++;
			}
			$q->where( 'event_date', 'lt', date( "Y-m-d G:i:s", mktime( 0, 0, 0, $month, 1, $year ) ) );
			$d = $q->execute();

			$res = array();
			foreach ($d as $ev) $res[ date('d',strtotime($ev->event_date)) ]++;
//			print_r( $res );
			return $res;
		}
	}
