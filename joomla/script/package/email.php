 <?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------


class One_Script_Package_Email extends One_Script_Package
{
	public function getTemplate( $alias = null){
		$templateQ = One_Repository::selectQuery('mailtpl');
		if ($alias) $templateQ->where('alias','eq',$alias);
		$templateQ->setLimit(1);
		$templates = $templateQ->execute();
		
		if(count($templates) > 0)
			return $templates[0];
		
		return null;
	}

}

