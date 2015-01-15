<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val:param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Dataexport extends One_Script_Node_Abstract
{
	function execute( &$data, &$parent )
	{
		$param =  split(':', trim( $this->data ) );

		$exportTo  = strtolower( $param[ 0 ] );
		$type      = '';
		$extension = '';
		switch( strtolower( $param[ 0 ] ) )
		{
			case 'excel2007':
				$type      = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
				$extension = 'xlsx';
				$exportTo  = 'Excel2007';
				break;
			case 'excel5':
				$type      = 'application/excel';
				$extension = 'xls';
				$exportTo  = 'Excel5';
				break;
			case 'csv':
				$type      = 'text/csv';
				$extension = 'csv';
				$exportTo  = 'CSV';
				break;
			case 'pdf':
				$type      = 'application/pdf';
				$extension = 'pdf';
				$exportTo  = 'PDF';
				break;
//			case 'serialized':
//				$type      = '';
//				$extension = 'zip';
//				$exportTo  = 'Serialized';
//				break;
			default:
			case 'html':
				$type      = 'text/html';
				$extension = 'html';
				$exportTo  = 'HTML';
				break;
		}

		$filename = ( isset( $param[ 1 ] ) ) ? $param[ 1 ] : 'export';

		require_once( One_Vendor::getInstance()->getFilePath().'/phpexcel/PHPExcel.php' );
		require_once( One_Vendor::getInstance()->getFilePath().'/phpexcel/PHPExcel/IOFactory.php' );

		$phpexcel = new PHPExcel();
		$phpexcel->removeSheetByIndex(0);

		$data[ 'dataExportRows' ] = array();
		$this->executeChain( $this->chain, $data, $parent, $this );

		$counter = 0;
		foreach($data[ 'dataExportRows' ] as $sheet => $records)
		{
			$workSheet = $phpexcel->createSheet($counter);
			$phpexcel->setActiveSheetIndex($counter);
			$workSheet = $phpexcel->getActiveSheet();

			$workSheet->calculateColumnWidths(true);
			for($col = 'A'; $col != 'DZ'; $col++) {
				$phpexcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}

			$workSheet->setTitle($sheet);
			$workSheet->fromArray($records);

			$counter++;
		}
		$phpexcel->setActiveSheetIndex(0);

		header('Content-Type: ' . $type);
		header('Content-Disposition: attachment;filename="' . $filename . '.' . $extension . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($phpexcel, $exportTo );
		$objWriter->save('php://output');

		exit;
	}

}
