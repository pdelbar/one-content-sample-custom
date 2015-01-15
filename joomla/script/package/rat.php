<?php
//------------------------------------------------------------------
// Helper functions for rendering herorats
//------------------------------------------------------------------


class oneScriptPackageRat extends One_Script_Package
{
	function getRatInfo($ratNames){
		
		$ratQ = One_Repository::selectQuery('rat');
		
		if(count($ratNames) > 0){
			$ratOr = $ratQ->addOr();
			
			foreach($ratNames as $ratName){
				$ratOr->where('name', 'eq', $ratName);
			}
			
		}
		$ratQ->where('published', 'eq', 1);
		$ratQ->setOrder('name+');
		
		$rats = $ratQ->execute();
		
		return $rats;
		
	}
}