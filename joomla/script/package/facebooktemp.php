 <?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

require_once(dirname(__FILE__).'/../../facebook/application.php');

class One_Script_Package_Facebooktemp extends One_Script_Package
{

	public function registerUser(){

		$apps = array('appId'  => '442889259142875', 'secret' => '42916fccac83cb82f67ad86b5624dcc4', 'cookie' => true);
		$application = new Facebook_Application($apps);

		$user = $application->getCustomMe(array('method' =>'fql.query', 'query' => 'SELECT uid,first_name,last_name,pic_big,email,current_location,sex FROM user WHERE uid=me()'));
		$user = $user[0];

		$loginUrl = self::getLogin($application);

		if(is_null($user)){

			return $loginUrl;
		}

		$fbaction = JRequest::getVar('fbaction', '');

		$query = One_Repository::selectQuery('registratie');
		$query->where('email', 'eq', $user['email']);
		$query->setLimit(1);
		$registrations = $query->execute();

		if(count($registrations) > 0){
			if($fbaction == 'update'){

				self::addFBUser($user, $registrations[0]->id);
				self::postWallMessage($application);

				return 'updated';
			}

			return 'duplicate';
		}

		if($fbaction == 'register'){

			$feedsend = self::postWallMessage($application);

			if($feedsend){
				self::addFBUser($user);
				return 'register';
			}
		}

		return $loginUrl;
	}

	private static function getLogin($application){
			$redirect = JURI::base() . '?fbaction=register';

			$loginUrl = $application->getLoginUrl(array('scope' => 'email, read_stream, user_location, publish_stream', 'redirect_uri' => $redirect));

			return $loginUrl;
	}

	private static function addFBUser($user, $registrationId = 0){

		if($registrationId != 0){
			$registration = One_Repository::selectOne('registratie', $registrationId);
		}else{
			$registration = One_Repository::getInstance('registratie');
		}

		$registration->voornaam = $user['first_name'];
		$registration->familienaam = $user['last_name'];

		$fbimage = '/images/stories/registration/' . $user['uid'] . '_big.jpg';
		file_put_contents(JPATH_SITE . $fbimage, file_get_contents($user['pic_big']));

		if(is_array($user['current_location'])){
			$registration->city = $user['current_location']['city'];
			$registration->province = $user['current_location']['state'];
		}

		$registration->geslacht = $user['sex'];
		$registration->fbimage = $fbimage;
		$registration->email = $user['email'];
		$registration->published = 1;
		$registration->fbid = $user['uid'];
		$registration->newsletter = 1;

		if($registrationId != 0){
			$registration->update();
		}else{
			$registration->insert();
		}
	}

	private static function postWallMessage($application){

		$message = array(
			'message' => 'Ik ben een dertiger geworden. Pleit mee voor leefbare straten en pleinen! Surf naar www.ikbeneendertiger.be',
			'description' => 'Een schone buurt met ruimte voor groen, fietsers en voetgangers. Stads- en dorpskernen op maat van mensen in plaats van auto\'s. Ik werd zonet een dertiger omdat ik vind dat er in mijn buurt nog veel werk aan de winkel is. Pleit mee om voor leefbare straten en pleinen en word ook een dertiger!',
			'picture' => 'http://www.ikbeneendertiger.be/templates/ikbeneendertiger/img/fb-30er.gif',
			'caption' => '',
			'name' => 'Ik ben een dertiger',
			'link' => 'www.ikbeneendertiger.be'
		);

		return $application->sendFeed($message);
	}

}

