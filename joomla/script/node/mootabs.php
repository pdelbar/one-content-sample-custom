<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Mootabs extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent )
	{
		$doc = JFactory::getDocument();
		$doc->addScript( ONESITEPATH . 'lib/libraries/mootabs/mootabs1.2.js', 'text/javascript' );
		$doc->addStyleSheet( ONESITEPATH . 'lib/libraries/mootabs/mootabs1.2.css', 'text/css' );

		$param =  split(':', trim( $this->data ) );
		$id = md5( $param[ 0 ] . microtime( true ) );

		$mooScript = '
      function initMooTabs' . $id . '() {
      	myTabs' . $id . ' = new mootabs( "' . $id  . '", {
																changeTransition: "none",
																mouseOverClass: "over"
															} );
      }
      window.addEvent("domready", initMooTabs' . $id . ' );';
		$doc->addScriptDeclaration( $mooScript, 'text/javascript' );

		$prevTabs = $data[ 'mootabTitles' ];
		$data[ 'mootabTitles' ] = array();

		// execute the rest of the chain first so the titles can be set
		$innertabs = $this->executeChain( $this->chain, $data, $parent, $this );

		$content  = '<div id="' . $id . '">';
		$content .= '<ul id="UL' . $id . '" class="mootabs_title">';
		foreach( $data[ 'mootabTitles' ] as $tId => $title )
		{
			$content .= '<li title="' . $tId . '">' . $title . '</li>';
		}
		$content .= '</ul>';
		$content .= $innertabs;
		$content .= '</div>';

		$data[ 'mootabTitles' ] = $prevTabs;

		return $content;
	}
}
