<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Mootab extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent )
	{
		$param =  split(':', trim( $this->data ) );
		$id = md5( $param[ 0 ] . microtime( true ) );

		$content  = '<div id="' . $id . '" class="mootabs_panel">';
		$content .= $this->executeChain( $this->chain, $data, $parent );
		$content .= '</div>';

		$data[ 'mootabTitles' ][ $id ] = ( isset( $param[ 1 ] ) ) ? $param[ 1 ] : $param[ 0 ];

		return $content;
	}
}
