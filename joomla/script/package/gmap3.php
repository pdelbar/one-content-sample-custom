<?php
ini_set("display_errors", "on");
error_reporting( E_ALL &~ E_NOTICE );
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

  class oneScriptPackageGmap3 extends One_Script_Package
  {

  	public function showGMap( $dataForMarkers = array(), $fieldNames = array(), $objName = '', $placeMe = '', $directions = 0, $options = array() )
    {
    	global $mainframe;

    	$output = '';

	    $document = JFactory::getDocument();
	    $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');

	    $language = $options['langusage'];

	    if($language == ''){
	    	$language = 'nl-NL';
	    }

	    if($language == 'fr-FR'){
	    	$country = 'Belgique';
	    }else{
	    	$country = 'België';
	    }

	    if(substr(JURI::base(), 0, 5) == 'https'){
	    	$gapi = 'https://maps.googleapis.com/maps/api/js?v=3&sensor=true&language=' . $language;
	    }else{
	    	$gapi = 'http://maps.googleapis.com/maps/api/js?sensor=true&language=' . $language;
	    }

	    One_Vendor::requireVendor('jquery/one_loader');
		One_Vendor::getInstance()
			->loadScript($gapi, 'head', 80)
			->loadScript(One_Vendor::getInstance()->getSitePath() . '/gmapv3.js', 'head', 90);

	      $tmp = md5( microtime() );

	      if( $objName == '' )
	      	$mapName = 'localmap' . $tmp;
	      else
	      	$mapName = $objName;

	       if ($placeMe == '') {

	      		$divid = 'gmapL' . $tmp;
	      		$output = '
	      			<div class="gmap" id="' . $divid . '" style="width:' . $options['width'] . 'px;height:' . $options['height'] . 'px;text-align:center; border: 0px solid #00529B"></div>
	      		';
	      } else {
	      		$output = '<style>#topGoogle { height: ' . $options['height'] . 'px; } </style>';
	      		$divid = $placeMe;
	      }

          if( $directions == 1 )
	      {

	      	$destinationData = $dataForMarkers[0];

	      	$destination = $destinationData[$fieldNames['street']] . ' ' . $destinationData[$fieldNames['nr']] . ', ' .
	   			$destinationData[$fieldNames['postcode']] . ' ' . $destinationData[$fieldNames['city']] . ', ' . $country;

	      	$output .= '<div class="mapDirections"><br/><form action="">';
	      	$output .= '<label for="origin' . $mapName . '"><strong>Vertrek: </strong></label><br />';
			$output .= '<input class="textfield" type="text" size="80" maxlength="255" name="origin' . $mapName . '" id="origin' . $mapName . '" value="" /><br />';
			$output .= '<label for="destination' . $mapName . '"><strong>Bestemming: </strong></label><br />';
	      	$output .= '<input type="text" disabled="disabled" name="destination' . $mapName . '" id="destination' . $mapName . '" value="' . $destination . '" size="80" maxlength="255" />';
	      	$output .= '<br /><input id="' . $mapName . 'Submit" class="button" value="Routebeschrijving" type="button" /></form></div>';
	        $output .= '<div id="' . $mapName . 'Directions" ></div>';
	      }

	   	$output .= '
	      <script type="text/javascript">
	      	jQuery(document).ready(function(){
	              ' . $mapName . ' = new OneGMapV3( document.getElementById( "' . $divid. '" ), "' . $mapName . '", "' . $directionsDiv . '", ' . $directions . ');
	              ' . $mapName . '.showGMap(' . intval( $options['zoomlevel']) . ', ' . intval( $options['minzoomlevel']) .
	              ', {allowScroll:' . intval($options['noScroll'])  . ', streetView:' . intval($options['noStreetView']) . ', mapTypeControl:' . intval($options['noMapTypeControl']) .
	              ', panControl:' . intval($options['noPanControl']) . ', zoomControl:' . intval($options['noZoomControl']) . '});
	       ';

	   	if(count($dataForMarkers) > 0){
	   		foreach($dataForMarkers as $data){
	   			$address = $data[$fieldNames['street']] . ' ' . $data[$fieldNames['nr']] . ', ' .
	   			$data[$fieldNames['postcode']] . ' ' . $data[$fieldNames['city']];

	   			$output .= $mapName . '.setMarker("' . $data[$fieldNames['name']] . '", "' . $address . '", "' . $country . '"' .
	   						', ["' . $options['markerIcon'] . '", "' . $options['markerShadow'] . '"]);';
	   		}
	   	}



	   	if($directions == 1){

	   		$output.= 'jQuery("#' . $mapName . 'Submit").click(function(event){event.preventDefault;' .
	   			$mapName . '.getDirections(' . intval($options['suppressDefaultMarkers']) .
	   				', ["' . $options['startIcon'] . '", "' . $options['startShadow'] . '", "' . $options['endIcon'] . '", "' . $options['endShadow'] . '"]' .
	   				', ["' . $options['markerIcon'] . '", "' . $options['markerShadow'] . '"]);});';

	   		$output .= $mapName . '.setOriginMarker(["' . $options['originIcon'] . '", "' . $options['originShadow'] . '"]);';

	   	}

	  	$output .= '});</script>';

		/*$document =& JFactory::getDocument();
		$document->addScriptDeclaration( 'var liveURL = "' . JURI::base() . '";' );
		$document->addScriptDeclaration( 'var ' . $mapName . ' = null;' );*/

	    return $output;
    }

}
