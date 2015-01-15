<?php
//------------------------------------------------------------------
// package file : functions to handle files
//------------------------------------------------------------------

	class One_Script_Package_File extends One_Script_Package
	{
		function file_exists( $path )
		{
			return file_exists($path);
		}

	}
