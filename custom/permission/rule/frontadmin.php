<?php
/**
 * ---------------------------------------------------------------------------------------------------------
 * 	Everything is content. Content is everything.
 *
 * Copyright (C) 2008 delius bvba. All rights reserved.
 *
 * one|content is free software and is distributed under the GNU General Public License,
 * and as distributed it may include or be derivative of works licensed under the GNU
 * General Public License or other free or open source software licenses.
 * ---------------------------------------------------------------------------------------------------------
 **/
class One_Permission_Rule_Frontadmin extends One_Permission_Rule
{
	public function __construct( $options = array() )
	{
		parent::__construct( $options );
		$this->rules = array();
	}

	public function authorize( $args )
	{

		$juser = JFactory::getUser();
		
		if($juser->guest == 1)
			return false;
			
		if(in_array(7, $juser->groups) || in_array(8, $juser->groups))
			return true;
			
		if(!in_array(16, $juser->groups))
			return false;
		
		return true;

	}
}
