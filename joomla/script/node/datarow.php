<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Datarow extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent, &$myParent )
	{
		$currentSheet = (!isset($data['currentSheet']) || null === $data['currentSheet']) ? 'Worksheet' : $data['currentSheet'];

		$content = $this->executeChain( $this->chain, $data, $parent, $this );
		$data[ 'dataExportRows' ][$currentSheet][] = explode( '|', $content );
	}
}
