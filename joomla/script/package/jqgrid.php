<?php
// http://www.trirand.com/jqgridwiki/doku.php?id=wiki:how_to_install

class oneScriptPackageJqgrid extends One_Script_Package
{
	public static function grid( $schemeName, $model )
	{
		$view = new OneView( $schemeName, 'jqgrid' );
		$view->setModel( $model );
		$view->setAll(  array( 'scheme' => $schemeName ));

		$content = $view->show();
		return $content;
	}

	public static function init( $schemeName, $iniFile = "jqgrid"  )
	{
		$dom = OneRepository::getDom();

		$dom->add('<script language="javascript" type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>', '_head');
		$dom->add('<script language="javascript" type="text/javascript" src="'.ONESITEPATH.'lib/libraries/js/jquery-ui-1.8.15.custom.min.js"></script>', '_head');
		$dom->add('<script language="javascript" type="text/javascript" src="'.ONESITEPATH.'lib/libraries/js/jqgrid/grid.locale-en.js"></script>', '_head');
		$dom->add('<script language="javascript" type="text/javascript" src="'.ONESITEPATH.'lib/libraries/js/jqgrid/jquery.jqGrid.min.js"></script>', '_head');
		$dom->add('<link rel="stylesheet" href="'.ONESITEPATH.'lib/libraries/js/jqgrid/css/ui.jqgrid.css" type="text/css" />', '_head');
		$dom->add('<link rel="stylesheet" href="'.ONESITEPATH.'lib/libraries/js/jquery-ui-1.8.15.custom.css" type="text/css" />', '_head');
		$dom->add('<link rel="stylesheet" href="'.ONESITEPATH.'lib/libraries/js/jqgrid/css/cupertino/jquery-ui-1.8.5.custom.css" type="text/css" />', '_head');

		$dom->render();

		$formula = parse_ini_file( ONECUSTOMPATH . DS . 'views' . DS . $schemeName . DS . $iniFile . '.ini', true );

		$params = $formula['_params'];
		if ($params) {
			unset($formula['_params']);
		}
		$titles = array();
		$cols = array();
		foreach ($formula as $section => $content) {
			$col = array();
			$col['name'] = $section;
			foreach ($content as $at => $val) {
				if ($val == '_true') $val = 'true';
				if ($val == '_false') $val ='false';
				list($at,$sub) = explode('.',$at);
				switch ($at) {
					case 'header' :
						$titles[] = $val;
						break;

					case 'editoptionsview' :
						// create value like
						//		editoptions: {value:"FE:FedEx;IN:InTime;TN:TNT;AR:ARAMEX"}
						list($scheme,$view) = explode(':',$val);
						$ob = OneRepository::getInstance($scheme);
						$options = trim(oneScriptPackageOne::view($ob,$view));
						$col['editoptions'] = array( 'value' => $options );
						break;
					default :
						if ($sub) {
							$col[ $at ][ $sub] = $val;
						} else {
							$col[ $at ] = $val;
						}
						break;
				}
			}
			$cols[] = $col;
		}
		return array( $titles, $cols,$params );
	}

	public static function rowFormula( $scheme )
	{
		list( $titles, $cols, $params) = self::init( $scheme->name() );
		$formula = array();
		foreach ($cols as $col) {
			$formula[ $col['name'] ] = $col[ 'view' ];
		}
		return $formula;
	}


	public static function createRow( $model, $formula )
	{
		$row = array();
		foreach ($formula as $name => $view) {
			if ($view) {
				$row[] = trim(oneScriptPackageOne::view($model,$view));
			} else {
				$row[] = $model->$name;
			}
		}
		return $row;
	}

	public function myjson( $kv )
    {
       $first = 1;
       $s = '{';
       foreach ($kv as $k => $v)
       {
           if(!$first) {
               $s .= ' , ';
           }
           $s .= $k . ' : '
           ;
           if($v == 'true') {
               $s .= 'true';
           }
           else if($v == 'false') {
               $s .= 'false';
           }
           else if(is_array($v)) {
               $s .= self::myjson($v);
           }
           else if(strpos($v, '[') !== false && strpos($v, ']') !== false)
           {
               $s .= str_replace(array('[', ']'), array('{', '}'), $v);
           }
           else {
               $s .= '"'. $v. '"';
           }
           $first = 0;
       }
       $s .='}';

       return $s;
   }

}
