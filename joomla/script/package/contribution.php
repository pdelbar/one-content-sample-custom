<?php
//------------------------------------------------------------------
// Helper functions for rendering civi contributions
//------------------------------------------------------------------


class One_Script_Package_Contribution extends One_Script_Package
{
	protected static $civiUserId = 0;

	function getCurrency($currency){
		
		if(strtoupper($currency) == 'EUR')
			return '&euro;';
		elseif(strtoupper($currency) == 'DOL')
			return '$';
			
		return $currency;
			
	}
}