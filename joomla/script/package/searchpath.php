<?php

die('deprecated package dude');
class oneScriptPackageSearchpath extends One_Script_Package
{
	public function add( $path )
	{
		if( is_dir( $path ) )
		{
//			echo 'adding to loader: ' . $path;
			One_Script_Factory::addSearchPath( $path );
		}
	}

	public function clear()
	{
		One_Script_Factory::clearSearchPath();
	}
}