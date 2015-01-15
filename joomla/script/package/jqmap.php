<?php
 class oneScriptPackageJqmap extends One_Script_Package
 {

 	private static $_mapName;
 	private static $_markerCount;

 	public function buildMapcontainer($containerId = 'mapContainer', $width = '500px', $height = '300px', $classes = 'jqmap'){
		return '<div id="' . $containerId . '" class="' . $classes . '" style="width:' . $width . ';height:' . $height . '"></div>';
	}

 	public function showMap($containerId = 'mapContainer', $mapName = 'defaultMap', $centerlatlong = array(), $mapoptions = array(), $language = 'nl-NL'){

 		if(substr(JURI::base(), 0, 5) == 'https'){
	    	$gapi = 'https://maps.googleapis.com/maps/api/js?v=3&sensor=true&language=' . $language;
	    }else{
	    	$gapi = 'http://maps.googleapis.com/maps/api/js?sensor=true&language=' . $language;
	    }

	    One_Vendor::requireVendor('jquery/one_loader');
		One_Vendor::getInstance()
			->loadScript($gapi, 'head', 80)
			->loadScript(JURI::base() . '/media/js/jqmap/jqmap.js', 'head', 90)
			->loadScriptDeclaration($mapName . ' = new JQMap()', 'onload', 200)
			->loadScriptDeclaration($mapName . '.showMap("' . $containerId . '", ' . json_encode($centerlatlong) . ', ' . json_encode($mapoptions) . ')', 'onload', 205);

		self::$_markerCount = 210;
		self::$_mapName = $mapName;
	}

	public function addModelData($model, $fields = array(), $markericons = array(), $markerinfo = true, $infooptions = array(), $urlfields = array()){

		$scheme = $model[0]->getScheme();

		foreach($model as $m){

			$markerdata = array();

			foreach($fields as $field => $modelfield){
				if($scheme->hasAttribute($modelfield)){
					$markerdata[$field] = $m->$modelfield;
				}else{
					$markerdata[$field] = $modelfield;
				}
			}

			$infourl = array();

			if($markerinfo){

				foreach($urlfields as $field => $modelfield){
					if($scheme->hasAttribute($modelfield)){
						$infourl[$field] = $m->$modelfield;
					}else{
						$infourl[$field] = $modelfield;
					}
				}
			}

			self::addMarker($markerdata, $markericons, $markerinfo, $infooptions, $infourl);
		}
	}

	public function addMarker($markerdata, $markericons = array(), $markerinfo = true, $infooptions, $infourl){


		$markerdata = array_merge(array(
			'lat' => 0.00, 'long' => 0.00, 'title' => '', 'message' => '',
			'street' => '', 'number' => '', 'zipcode' => '', 'city' => '', 'country' => 'België'),
		$markerdata);

		$address = '';

		if($markerdata['title'] == '' || ($markerdata['lat'] == 0.00 || $markerdata['long'] == 0.00)){
			$address = trim($markerdata['street'] . ' '  . $markerdata['number']);

			if($markerdata['zipcode'] != '' || $markerdata['city'] != '')
				$address .= trim(', ' . $markerdata['zipcode'] . ' ' . $markerdata['city']);

			if($markerdata['title'] == '')
				$markerdata['title'] = $address;
		}

		if($markerdata['message'] == '' && $markerdata['title'] != $address)
			$markerdata['message'] = $address;

		$declaration = 'mapMarker = ' . self::$_mapName;

		if($markerdata['lat'] != 0.00 && $markerdata['long'] != 0.00){
			$declaration .= '.setLatLongMarker(' . $markerdata['lat'] . ', ' . $markerdata['long'] . ', ';
		}else{
			$declaration .= '.setAddressMarker("' . $address . ', ' . $markerdata['country'] . '", ';
		}

		$declaration .= '"' . $markerdata['title'] . '", "' . $markerdata['message'] . '", ' . json_encode($markericons) . ', ' . intval($markerinfo) . ', ' . json_encode($infooptions) . ')';

		One_Vendor::getInstance()->loadScriptDeclaration($declaration, 'onload', 210);

	}

}