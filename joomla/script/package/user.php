<?php
/**
 * Performs calls to XAPI and handles the rendering of the data
 * @author traes
 */
class oneScriptPackageUser extends One_Script_Package
{
/**
	 * Current User
	 * @var One_Model
	 */
	protected static $_user = null;
	
	//joomla user id
	protected static $_userId = 0;

	/**
	 * Get the current user
	 * @return One_Model
	 */
	public static function getUser()
	{
		if(null === self::$_user) {
			$juser = JFactory::getUser();
			if(1 == $juser->guest) {
				return null;
			}

			$extQ = One_Repository::selectQuery('extendeduser');
			$extQ->where('juser:id', 'eq', $juser->id);
			$users = $extQ->execute();

			if(0 == count($users)) {
				return null;
			}

			self::$_user = $users[0];
		}

		return self::$_user;
	}
	
	function getUserId(){
		
		if(self::$_userId == 0){
			
			$juser = JFactory::getUser();
			
			if($juser->guest != 1)
				self::$_userId = $juser->id;
			
		}
		
		return self::$_userId;
	}
}
