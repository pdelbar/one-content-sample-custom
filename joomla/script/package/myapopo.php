<?php

//------------------------------------------------------------------
// Pledge functionaliteit
//------------------------------------------------------------------

class oneScriptPackageMyapopo extends One_Script_Package {

  function getDashboardUserId() {

    $juser = JFactory::getUser();

    if ($juser->guest)
      return 0;

    if (JRequest::getInt('user', 0) > 0 && (in_array(7, $juser->groups) || in_array(8, $juser->groups))) {
      $_SESSION['DASHUSER'] = JRequest::getInt('user', 0);
      return JRequest::getInt('user', 0);
    }

    if ($_SESSION['DASHUSER'] > 0)
      return $_SESSION['DASHUSER'];

    return $juser->id;
  }

  function getPublicPageUserId() {
    $pmid = JRequest::getInt('id',0);
    if ($pmid) {
      $pm = One_Repository::selectOne('postmessage',$pmid);
      return $pm->user_id;
    }
    return 0;
  }
  
  function getPublicRat() {
    $pmid = JRequest::getInt('id',0);
    if ($pmid) {
      $pm = One_Repository::selectOne('postmessage',$pmid);
      if ($pm->subject_type != 'herorat') return null;
      $subid = $pm->subject_id;
      $rat = new One_Helper_Gamify_Civi_Herorat($subid);
      return $rat;
    }
    return null;
  }
  
  
  function getGamerFromJuser( $userid ) {
    $contact = new One_Helper_Gamify_Civi_Gamer($userid);
    return $contact;
  }
  
  
  function isAdministrator() {

    $juser = JFactory::getUser();

    if ($juser->guest)
      return 0;

    if (in_array(7, $juser->groups) || in_array(8, $juser->groups))
      return 1;

    return 0;
  }

  function getDashboardUsers() {
    $juserQ = One_Repository::selectQuery('juser');
    $juserQ->where('jusergroups:id', 'in', array(7, 8, 15));
    $juserQ->setOrder('name+');
    $juserQ->setGroup('id');
    $jusers = $juserQ->execute();

    return $jusers;
  }

  function getMessages($userId) {

    $messageQ = One_Repository::selectQuery('postmessage');
    $messageQ->where('published', 'eq', 1);
    $messageQ->where('type', 'in', array('story', 'news'));
    $messageQ->where('user_id', 'eq', $userId);
    $messageQ->setOrder('created-');
    $messageQ->setLimit(10);

    $messages = $messageQ->execute();

    return $messages;
  }

  function renderMessages($userId) {

    $messageUrl = 'index.php?option=com_one&scheme=postmessage&task=list&view=list&query=myapopo&tmpl=naked&Itemid=524&count=10&user=' . $userId;
    $pageUrl = 'index.php?option=com_one&scheme=postmessage&task=show&view=pagination&tmpl=naked&Itemid=524&user=' . $userId;

    One_Vendor::getInstance()
            ->loadScriptDeclaration('renderMessages("#messagetabs", "' . $messageUrl . '", "' . $pageUrl . '", false);', 'onload', 220)
            ->loadScriptDeclaration('renderMessages("#messagepagination", "' . $messageUrl . '", "' . $pageUrl . '", true);', 'onload', 230)
            ->loadScriptDeclaration('showFullMessage()', 'onload', 240);
  }

  function getPagesCount($userId, $type) {

    $messageQ = One_Repository::selectQuery('postmessage');
    $messageQ->where('published', 'eq', 1);

    if ($type)
      $messageQ->where('type', 'eq', $type);
    else
      $messageQ->where('type', 'in', array('story', 'news'));

    $messageQ->where('user_id', 'eq', $userId);


    $pages = ceil($messageQ->getCount() / 10);

    return $pages;
  }

  function getBadges($userId, $allbadges = false) {
    $juser = One_Repository::selectOne('juser', $userId);
    $options = array('orderBy' => 'id-');
    if (!$allbadges)
      $options['limit'] = 2;

    $badges = $juser->getRelated('badges', $options);
    return $badges;

    /*
      $messageQ = One_Repository::selectQuery('postmessage');
      $messageQ->where('published', 'eq', 1);
      $messageQ->where('type', 'eq', 'badge');
      $messageQ->where('user_id', 'eq', $userId);
      $messageQ->setOrder('title+');
      $messagecount = $messageQ->getCount();

      if(!$allbadges)
      $messageQ->setLimit(4);

      $messages = $messageQ->execute();

      return array($messages, $messagecount);
     */
  }

  function getUniqueBadges($userId) {
    $q = One_Repository::selectQuery('badge');
    $q->setRaw('SELECT DISTINCT b.id, b.* FROM fgmkz_badge b JOIN fgmkz_user_badges ub ON ub.badge_id = b.id WHERE user_id = ' . $userId);
    $badges = $q->execute();
    return $badges;
  }


  function getImpactOverview($userId) {

    $language = strtolower(substr(JFactory::getLanguage()->getTag(), 0, 2));

    $impactQ = One_Repository::selectQuery('impacttype');
    $impactQ->setRaw('SELECT 
							it.id AS id, 
							sum(u.amount) AS total,
							it.unit AS unit,
							it.title AS title,
							it.description AS description,
							it.image AS image
							FROM fgmkz_impacttype it
							JOIN fgmkz_userimpactcounter u ON (u.impacttype_id = it.id) 
							WHERE u.user_id = ' . $userId . ' AND it.language = "' . $language . '" 
							GROUP BY it.id ORDER BY description ASC;'
    );

    $impacts = $impactQ->execute();

    return $impacts;
  }

  function getAvailableAvatars($stage) {

    $avatarQ = One_Repository::selectQuery('avatar');
    $avatarQ->setRaw('SELECT a.id AS id, 
							a.name AS name 
							FROM fgmkz_avatar a 
							JOIN fgmkz_avatars_stages ast ON (a.id = ast.avatar_id) 
							JOIN fgmkz_stage st ON (st.id = ast.stage_id) 
							WHERE published = 1 AND st.alias = "' . $stage . '" GROUP BY a.id ORDER BY a.name ASC;');

    $avatars = $avatarQ->execute();

    return $avatars;
  }

  function getAvatarPath($avatarname, $field, $mood) {

    $url = 'images/avatar/' . $avatarname . '/';

    if ($field)
      $url .= strtolower($field) . '/';


    if ($mood && file_exists(constant('JPATH_SITE') . '/' . $url . strtolower($mood) . '.png'))
      $url .= strtolower($mood) . '.png';
    else
      $url .= 'normal.png';

    return JURI::base() . $url;
  }

  function avatarSelector() {

    One_Vendor::getInstance()
            ->loadScriptDeclaration('avatarSelector()', 'onload', 210);
  }

  function userSwitcher() {

    $baseurl = JRoute::_('index.php?Itemid=' . JRequest::getInt('Itemid', 0));

    One_Vendor::getInstance()
            ->loadScriptDeclaration('userSwitcher("' . $baseurl . '")', 'onload', 210);
  }

  public function increaseImpact($userid, $ctr, $amount) {
    $q = One_Repository::selectQuery('userimpactcounter');
    $q->where('impacttype_id', 'eq', $ctr->impacttype_id);
    $q->where('user_id', 'eq', $userid);
    $recs = $q->execute();
    if (count($recs)) {
      $rec = $recs[0];
      //echo ' / found counter #', $rec->id, ' at ', $rec->amount;
      $originalAmount = $rec->amount;
      $rec->amount += $amount;
      $rec->lastupdate = date('Y-m-d H:i:s');
      $rec->update();
    } else {
      //echo ' / creating new counter';
      $rec = One::make('userimpactcounter');
      $rec->impacttype_id = $ctr->impacttype_id;
      $rec->user_id = $userid;
      $originalAmount = 0;
      $rec->amount = $amount;
      $rec->lastupdate = date('Y-m-d H:i:s');
      $rec->insert();
    }
    $newAmount = $rec->amount;
    echo ' (now ', $newAmount, ')';

    // check for impact badges to donate
    $q = One_Repository::selectQuery('badge');
    $q->where('metric_id', 'eq', $ctr->impacttype_id);
    $q->where('metric_value', '>', $originalAmount);
    $q->where('metric_value', '<=', $newAmount);
    $q->where('published', 'eq', 1);
    $badges = $q->execute();
    foreach ($badges as $badge) {
      echo '<br>Should get badge ', $badge->name;
      self::_giveImpactBadge($badge, $userid);
    }
  }

  private function _giveImpactBadge($badge, $userid) {
    $user = oneScriptPackageJoomla::getUser($userid);
    $gamer = new One_Helper_Gamify_Civi_Gamer($userid);
    $context = array(
        'gamer' => $gamer,
        'subject' => $gamer,
        );
    // prepare data
    $content = One_Helper_Gamify_Actiontype_Badge::getBadgeData($badge, $user);
    if (!$content)
      return 'No content for EN';
    
    // add title for FB
    $content['fbmessage'] = "%DONORNAME%'s just got the " . $badge->name . "' badge !";    
    $content = One_Helper_Gamify_Actiontype_Abstract::replaceTokens($context, $content);

   	// give badge
    $badge->addRelated('users',$userid);
    $badge->update();
    
    // post message
    $message = One::make('postmessage');
    $message->user_id = $userid;

    $message->title = $content['title'];
    $message->description = $content['description'];
    if ($content['image']) {
      $message->image = 'http://www.apopo.org' . $content['image'];
    }

    $message->subject_type = 'Contact';
    $message->subject_id = $userid;
    $message->published = 1;
    $message->created = strftime('%Y-%m-%d %H:%M:%S', strtotime('now'));
    $message->type = 'badge';
    $message->insert();
    
    // Facebook
    $content['pmlink'] = 'http://www.apopo.org/pm/message/' . $message->alias;
    $content['pmintro'] = strip_tags($content['description']);
    $message = One::make('postmessage');

    $message->user_id = $userid;

    $message['description'] = $content['fbmessage'];
    $message['title'] = $content['title'];
 
    $message['pmlink'] = $content['pmlink'];
    $message['pmintro'] = $content['pmintro'];
    
    $message->image = 'http://www.apopo.org' . $content['image'];

    $message->published = 1;
    $message->created = strftime('%Y-%m-%d %H:%M:%S', strtotime('now'));
    $message->type = 'facebook';
    $message->insert();
  }  
  
  
  public function giveGameBadge($rat, $userid) {
    // select a badge
    $type = $rat['herorat_field'];
    echo '<br/>Selecting a <b>', $type, '</b> badge ... ';
    $q = One_Repository::selectQuery('badge');
    $q->setRaw("
      SELECT 
        *
      FROM 
        fgmkz_badge
      WHERE 
        group_id = 2
        AND
        type = '$type'
      ORDER BY 
        rand()
      LIMIT 1
      ");
    $badges = $q->execute();
    if (count($badges) == 0) {
      echo ' found no badges.';
      return;
    }
    $badge = $badges[0];
    echo ' selected \'', $badge->name, "'";
    
    $user = oneScriptPackageJoomla::getUser($userid);
    $gamer = new One_Helper_Gamify_Civi_Gamer($userid);
    $subject = new One_Helper_Gamify_Civi_Herorat($rat['id']);
    $context = array(
        'gamer' => $gamer,
        'subject' => $subject,
        );

    // prepare data
    $content = One_Helper_Gamify_Actiontype_Badge::getBadgeData($badge, $user);
    if (!$content)
      return 'No content for EN';

    // add title for FB
    $content['fbmessage'] = "%RATNAME%'s just got the " . $badge->name . "' badge !";    
    $content = One_Helper_Gamify_Actiontype_Abstract::replaceTokens($context, $content);

   	// give badge
    $badge->addRelated('users',$userid);
    $badge->update();
    
    // post message
    $message = One::make('postmessage');
    $message->user_id = $userid;

    $message->title = $content['title'];
    $message->description = $content['description'];
    if ($content['image']) {
      $message->image = 'http://www.apopo.org' . $content['image'];
    }

    $message->subject_type = 'Membership';
    $message->subject_id = $rat['id'];
    $message->published = 1;
    $message->created = strftime('%Y-%m-%d %H:%M:%S', strtotime('now'));
    $message->type = 'badge';
    $message->insert();
    
    // Facebook
    $content['pmlink'] = 'http://www.apopo.org/pm/message/' . $message->alias;
    $content['pmintro'] = strip_tags($content['description']);
    $message = One::make('postmessage');

    $message->user_id = $userid;

    $message['description'] = $content['fbmessage'];
    $message['title'] = $content['title'];
 
    $message['pmlink'] = $content['pmlink'];
    $message['pmintro'] = $content['pmintro'];
    
    $message->image = 'http://www.apopo.org' . $content['image'];

    $message->published = 1;
    $message->created = strftime('%Y-%m-%d %H:%M:%S', strtotime('now'));
    $message->type = 'facebook';
    $message->insert();
  }  
  
  
  
  function getImpactTokens() {
    $q = One_Repository::selectQuery('impacttype');
    $its = $q->execute();
    $tokens = array();
    foreach ($its as $it) $tokens[ $it->tokencode ] = $it;
    return $tokens;
  }

}