<?php
//-------------------------------------------------------------------------------------------------
// {iomchart chartname:params}
//
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Pdf extends One_Script_Node_Abstract
{

	function execute( &$data, &$parent )
	{
		$param =  split(':', trim( $this->data ) );

		$xml = $this->executeChain( $this->chain, $data, $parent, $this );

		if( in_array( 'debug', $param ) )
			return '<pre>' . htmlentities( $xml ) . '</pre>';
		else
		{
			$xml = preg_replace( '/&(?![a-z0-9#]+;)/is', '&amp;', $xml );
			require_once( One::getInstance()->getPath() . DS . '../vendor' . DS . 'genpdf.php' );
//			ob_clean();
//			$document = &JDocument::getInstance( );
//			$document->setBuffer('');
//			$document->setCharset('iso-8859-1');
			handleXML( $xml );
			exit;
		}
	}

}
