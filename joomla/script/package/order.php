<?php


class oneScriptPackageOrder extends One_Script_Package
{
	function getOgoneStatusDescription($statusId = 0)
	{
		$status = One_Repository::selectOne('ogonestatus', $statusId);
		
		if($status)
			return $status->description;
			
		return '';
		
	}
	
	public static function processForm(){
		
		$language = JFactory::getLanguage()->get('tag');
		
		if($language == 'fr-FR')
			$itemId = '107';
		else
			$itemId = '104';
		
		$url = JRoute::_('index.php?option=com_one&scheme=order&task=ogonecheckout&view=ogoneform&Itemid=' . $itemId);

		One_Vendor::getInstance()
			->loadScript(JURI::base() . 'media/js/order.js', 'onload', 300)
			->loadScriptDeclaration('submitOrderForm("#orderSubmit", "#orderForm", "' . $url . '", true);', 'onload', 220)
			->loadScriptDeclaration('groupInit();', 'onload', 230)
			->loadScriptDeclaration('certificateInit();', 'onload', 235);
			//->loadScriptDeclaration('amountSelection();', 'onload', 240);
	}
	
	public static function getOrderOgm(){
			
			$session = One_Repository::getSession();
			
			if(!$session->varExists('reference', 'donateorder'))
				return '';
				
			$orderQ = One_Repository::selectQuery('order');
			$orderQ->where('reference', 'eq', $session->get('reference', 'donateorder'));
			$orders = $orderQ->execute();
		
			if(count($orders) <= 0)
				return '';
				
			$order = $orders[0];
			
			return $order->ogm;
			
	}
	
	public static function getFormvalues($formtype = 'customer'){
		
		$formValues = JRequest::getVar($formtype . 'Form', array());
		
		$session = One_Repository::getSession();
			
		if($session->varExists($formtype, 'donateorder')){
			$sessionValues = $session->get($formtype, 'donateorder');
			
			if(is_array($sessionValues))
				$formValues = array_merge($formValues, $sessionValues);
		
		}
		
		if($formtype == 'order'){
			$campaignParams = array('action_id', 'campaign_id', 'utm_source', 'utm_medium', 'utm_campaign');
			
			foreach($campaignParams as $campaignParam){
				
				$scheme = strval(JRequest::getVar('scheme', ''));
				$view = strval(JRequest::getVar('view', ''));
				
				if($campaignParam == 'action_id' && $scheme == 'landingpage' && $view == 'pbl_detail')
					$campaignValue = intval(JRequest::getVar('id', ''));
				else
					$campaignValue = strval(JRequest::getVar($campaignParam, ''));
				
				if($campaignValue != '')
					$formValues[$campaignParam] = $campaignValue;
				elseif($campaignParam == 'utm_source' && array_key_exists('action_id', $formValues))
					$formValues[$campaignParam] = $formValues['action_id'];
				elseif($campaignParam == 'utm_campaign' && array_key_exists('campaign_id', $formValues))
					$formValues[$campaignParam] = $formValues['campaign_id'];
			
			}
		}
		
		return $formValues;
		
	}
	
	
}
