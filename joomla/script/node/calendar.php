<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Calendar extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent )
	{
		$params =  split(':', trim( $this->data ) );
		$atts = array();
		foreach( $params as $param )
		{
			$keyval = preg_split( '/=/', $param );

			switch( strtolower( $keyval[ 0 ] ) )
			{
				case 'begindate':
					$startdate           = substr( $keyval[ 1 ], 0, 4 ) . '-' . substr( $keyval[ 1 ], 4, 2 ) . '-' . substr( $keyval[ 1 ], 6, 2 ) . ' ' . substr( $keyval[ 1 ], 9, 2 ) . ':' . substr( $keyval[ 1 ], 11, 2 ) . ':' . substr( $keyval[ 1 ], 13, 2 );
					$atts[ 'startdate' ] = $startdate;
					break;
				case 'enddate':
					$enddate           = substr( $keyval[ 1 ], 0, 4 ) . '-' . substr( $keyval[ 1 ], 4, 2 ) . '-' . substr( $keyval[ 1 ], 6, 2 ) . ' ' . substr( $keyval[ 1 ], 9, 2 ) . ':' . substr( $keyval[ 1 ], 11, 2 ) . ':' . substr( $keyval[ 1 ], 13, 2 );
					$atts[ 'enddate' ] = $enddate;
					break;
				case 'agendas':
					$ids = preg_split( '/(\s*);(\s*)/', trim( $keyval[ 1 ] ) );
					foreach( $ids as $key => $id )
					{
						if( trim( $id ) != '' )
							$ids[ $key ] = $id;
						else
							unset( $ids[ $key ] );
					}

					$atts[ 'category' ] = $ids;
					break;
			}
		}

		if( !isset( $atts[ 'startdate' ] ) )
			$atts[ 'startdate' ] = date( 'Y-m-d' ) . ' 00:00:00';

		$eventFac = OneRepository::getFactory( 'event' );
		$eQ       = $eventFac->selectQuery();
		$eQ->where( 'status', 'eq', 2 );
		$eQ->where( 'startdate', 'gte', $atts[ 'startdate' ] );

		if( isset( $atts[ 'enddate' ] ) )
			$eQ->where( 'startdate', 'lte', $atts[ 'enddate' ] );

		if( isset( $atts[ 'category' ] ) )
			$eQ->where( 'category', 'in', $atts[ 'category' ] );

		$eQ->setOrder( 'startdate+' );

		$events = $eQ->execute();

		$view =& new OneView( 'event', 'callist' );
		$view->setModel( $events );
		$content = $view->show();
		return $content;
	}
}
