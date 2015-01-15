<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class oneScriptPackageJqajax extends One_Script_Package
{

	function defaultView($url = '', $container = '#viewContainer', $params = array()){

		if($url != ''){

			$params = json_encode($params, true);
			$declaration .= 'loadAjaxview("' . $url . '", "' . $container  . '", ' . $params . ');';

			self::ajaxVendor('view', $declaration);
		}
	}

	function triggerView($elements = array(), $params = array()){

		$elements = json_encode($elements);
		$params = json_encode($params);

		$declaration .= 'triggerAjaxview(' . $elements . ', ' . $params  . ');';

		self::ajaxVendor('view', $declaration);
	}

	function launchSearch($elements = array(), $params = array(), $extrafields = array()){

		$elements = json_encode($elements);
		$params = json_encode($params);
		$extrafields = json_encode($extrafields);

		$declaration .= 'searchAjaxview(' . $elements . ', ' . $params  . ', '. $extrafields . ');';

		self::ajaxVendor('view', $declaration);
	}

	function postData($url = '', $event = 'click', $postdata = array(), $trigger = '.postSubmit', $params = array()){

		$postdata = json_encode($postdata);
		$params = json_encode($params);

		if($url != ''){
			if($event == 'ready' || $event == 'both')
				$declaration .= 'ajaxSubmit("' . $url . '", ' . $postdata . ', ' . $params . ');';
			if($event == 'click' || $event == 'both')
				$declaration .= 'ajaxTriggerClick("' . $trigger . '", "' . $url . '", ' . $postdata . ', ' . $params . ');';


			self::ajaxVendor('post', $declaration);
		}
	}

	function postForm($url = '', $form = '#ajaxForm', $submitbutton = '#formSubmit', $params = array(), $extravalues = array()){


		$params = json_encode($params);
		$extravalues = json_encode($extravalues);

		if($url != ''){
			$declaration .= 'postAjaxForm("' . $url . '", "' . $form . '", "' . $submitbutton . '", ' . $params . ', ' . $extravalues . ');';

			self::ajaxVendor('post', $declaration);
		}

	}

	function postDataOne($scheme = '', $action = 'update', $urlextra = array(), $event ='click', $postdata = array(), $trigger = '.postSubmit', $params = array()){

		$url = self::getPostUrl($scheme, $action, $urlextra);

		self::postData($url, $event, $postdata, $trigger, $params);
	}

	function postFormOne($scheme = '', $action = 'update', $urlextra = array(), $form = '#ajaxForm', $submitbutton = '#formSubmit', $params = array(), $extravalues = array()){

		$url = self::getPostUrl($scheme, $action, $urlextra);

		self::postForm($url, $form, $submitbutton, $params, $extravalues);
	}

	function getPostUrl($scheme = '', $action= 'update', $extra = array()){

		$postUrl = 'index.php?option=com_one&scheme=' . $scheme . '&task=ajax' . $action;

		foreach($extra as $name => $value)
			$postUrl .= '&' . $name . '=' . $value;

		return $postUrl;
	}

	protected function ajaxVendor($type = '', $declaration = ''){

		$vendor = One_Vendor::getInstance();

		if($type != '')
			$vendor->loadScript(JURI::base() . '/media/js/jqcontrol/ajax' . $type . '.js', 'head', 100);
		if($declaration != '')
			$vendor->loadScriptDeclaration($declaration, 'onload', 110);

	}
}