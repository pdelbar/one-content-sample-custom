<?php
ini_set("display_errors", "on");
error_reporting( E_ALL &~ E_NOTICE );
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

  class oneScriptPackageReversegeo3 extends One_Script_Package
  {

  	public function showGMap( $objName = '', $placeMe = '', $options = array(), $geotypes = array(), $extraparams = array())
    {
    	global $mainframe;

    	$output = '';

	    $document = JFactory::getDocument();
	    $document->setMetaData('viewport', 'initial-scale=1.0, user-scalable=no');

	    $language = $options['language'];

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

	     if(count($geotypes) > 0){

	   		$output .= '<div id="' . $mapName . 'GeoResult">';

	   		foreach($geotypes as $geotype){
	   			$output .= '<input type="hidden" value="" id="' . $mapName . $geotype . '" />';
	   		}

	   		$output .='</div>';
	    }


	    $output .= '<script type="text/javascript">';
	   $output .= 'function processGeo(georeturn, extraparams){}';
	    $output	.= 'jQuery(document).ready(function(){
	              ' . $mapName . ' = new OneGMapV3( document.getElementById( "' . $divid. '" ), "' . $mapName . '")
	              ' . $mapName . '.showGMap(' . intval( $options['zoomlevel']) . ', ' . intval( $options['minzoomlevel']) .
	    	      ', {allowScroll:' . intval($options['noScroll'])  . ', streetView:' . intval($options['noStreetView']) . ', mapTypeControl:' . intval($options['noMapTypeControl']) .
	              ', panControl:' . intval($options['noPanControl']) . ', zoomControl:' . intval($options['noZoomControl']) . '});';

	    $returngeo = json_encode($geotypes);

	    if($options['processgeo'] == 1 && count($extraparams) > 0){
	    	$functionextras = json_encode($extraparams);
	    }else{
	    	$functionextras = '[]';
	    }

	   $output .= $mapName . '.reverseGeo(["' . $options['markerIcon'] . '", "' . $options['markerShadow'] . '"], ' . $returngeo . ', ' . intval($options['hiddenfields']) . ', ' . intval($options['processgeo']) .  ', ' . $functionextras . ');';


	   $output .= '});</script>';

	   	return $output;
    }
}
