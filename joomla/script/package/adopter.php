 <?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

require_once(dirname(__FILE__).'/../../facebook/application.php');

class One_Script_Package_Adopter extends One_Script_Package
{
	public function getCurrentUserExtras(){
		
		$juserid = oneScriptPackageMyapopo::getDashboardUserId();
		$extraQ = One_Repository::selectQuery('juserextra');
		$extraQ->where('user_id', 'eq', $juserid);
		$extraQ->setOrder('id-');
		$extra = $extraQ->execute();
		if(count($extra) > 0)
			return $extra[0];
    
    // none found, create one
    $ex = One_Repository::getInstance('juserextra');
    $ex->user_id = $juserid;
    $ex->mailfrequency = 7;
    $ex->fbpermission = 0;
    $ex->$fbexpired = 1;
    $ex->insert();
		return $ex;
	}
	
	public function getAdopters($permission = 1, $expired = 0){
		$adopterQ = One_Repository::selectQuery('juserextra');
		
		$adopterQ->where('fbpermission', 'eq', $permission);
		$adopterQ->where('fbexpired', 'eq', $expired);
		
		$adopters = $adopterQ->execute();
		
		return $adopters;
	}

}

