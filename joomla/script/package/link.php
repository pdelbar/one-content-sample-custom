<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

	class oneScriptPackageLink extends One_Script_Package
	{
		function addHttp($url)
		{
			if (substr($url, 0, 7) == "http://")
			{
				return $url;
			}
			else
			{
				return "http://" . $url;
			}
		}
	}
