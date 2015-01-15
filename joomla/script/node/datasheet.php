<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Datasheet extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent, &$myParent )
	{
		$data['dataExportRows'][$this->data] = array();
		$data['currentSheet'] = $this->data;
		$this->executeChain( $this->chain, $data, $parent, $this );
		unset($data['currentSheet']);
		
	}
}
