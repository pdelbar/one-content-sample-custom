<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class oneScriptPackageJqcontrol extends One_Script_Package
{
	function launchReady($functionname = '', $section = 'head', $weight = 100, $params = array()){

		$jsParams = '';

		if(count($params) > 0){

			foreach($params as $param){

				if($jsParams != '')
					$jsParams .= ', ';

				if(is_array($param)){

					$jsParams .= json_encode($param);

				}else{
					$jsParams .= '"' . $param . '"';
				}
			}
		}

		$declaration = 'jQuery(document).ready(function(){';
		$declaration .= $functionname . '(' . $jsParams . ');';
		$declaration .= '});';

		$vendor = One_Vendor::getInstance();
		$vendor->loadScriptDeclaration($declaration, $section, $weight);
	}

	function launchFaq($elements = array(), $hover = true, $params = array(), $hoverparams = array()){

		$elements = json_encode($elements);
		$params = json_encode($params);
		$hoverparams = json_encode($hoverparams);

		$declaration .= 'faqList(' . $elements . ', ' . $params . ');';
		if($hover)
			$declaration .= 'faqHover(' . $elements . ', ' . $hoverparams . ');';

		$vendor = One_Vendor::getInstance();
		$vendor->loadScript(JURI::base() . '/media/js/jqcontrol/faq.js', 'head', 100);
		$vendor->loadScriptDeclaration($declaration, 'onload', 110);

	}

	function launchCounter($targetvalue = 0, $counter = '#counterField', $params = array()){

		$params = json_encode($params);

		$declaration .= 'initCounter("' . $counter . '", ' . $targetvalue . ', ' . $params . ');';

		$vendor = One_Vendor::getInstance();
		$vendor->loadScript(JURI::base() . '/media/js/jqcontrol/counter.js', 'head', 100);
		$vendor->loadScriptDeclaration($declaration, 'onload', 110);
	}

	function launchCleanlink($data = array(), $trigger = '.redirectTrigger'){


		$data = json_encode($data);

		$declaration .= 'cleanLink(' . $data . ', "' . $trigger . '");';

		$vendor = One_Vendor::getInstance();
		$vendor->loadScript(JURI::base() . '/media/js/jqcontrol/cleanlink.js', 'head', 100);
		$vendor->loadScriptDeclaration($declaration, 'onload', 110);

	}

	function launchImagerow($overlay = array(), $description = array(), $selectors = array(), $classes = array()){


		$overlay = json_encode($overlay);
		$description = json_encode($description);
		$selectors = json_encode($selectors);
		$classes = json_encode($classes);

		$declaration .= 'imageRow(' . $overlay . ', ' . $description . ', ' . $selectors . ', ' . $classes . ');';

		$vendor = One_Vendor::getInstance();
		$vendor->loadScript(JURI::base() . '/media/js/jqcontrol/imagerow.js', 'head', 100);
		$vendor->loadScriptDeclaration($declaration, 'onload', 110);

	}

	function loadJquery(){
		One_Vendor::getInstance()->requireVendor('jquery/one_loader');
	}

	function loadChosen(){

		$vendor = One_Vendor::getInstance()
			->loadStyle('jquery/css/chosen/chosen.css', 'head', 100)
			->loadScript('jquery/js/chosen.jquery.js', 'head', 200)
			->loadScriptDeclaration('jQuery(".chzn-select").chosen({allow_single_deselect: true});', 'body', 220);
	}

	function launchGallery($triggerSelector, $targetSelector){
		One_Vendor::getInstance()
			->loadScriptDeclaration('launchGallery("' . $triggerSelector . '", "' . $targetSelector . '");', 'onload', 200);
	}

}