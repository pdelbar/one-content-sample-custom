<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

	class oneScriptPackageForms extends One_Script_Package
	{
		function createForm(OneScheme $scheme, $formname = '', $action = '')
		{
			return OneFormContainerFactory::createForm($scheme, $formname, $action);
		}
	}
