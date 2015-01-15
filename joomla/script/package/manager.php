<?php

//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

class oneScriptPackageManager extends One_Script_Package {

  //Get all topics
  function getTopics() {
    $topicQ = One_Repository::selectQuery("topic");
    $topicQ->setRaw("
			select * from #__topics order by name asc
		");
    $topics = $topicQ->execute();
    return $topics;
  }

  //Get all pricesets
  function getPricesets() {
    $pricesetQ = One_Repository::selectQuery("priceset");
    $pricesetQ->setRaw("
			select * from #__priceset order by name asc
		");
    $pricesets = $pricesetQ->execute();
    return $pricesets;
  }

  //Get the active joomla languages
  function getLanguages() {
    $languageQ = One_Repository::selectQuery("language");
    $languageQ->setRaw("
			select * from #__languages
		");
    $languages = $languageQ->execute();
    return $languages;
  }

  //Get the mail templates
  function getMailtemplates() {
    $mailtplQ = One_Repository::selectQuery("mailtpl");
    $mailtplQ->setRaw("
			select * from #__mailtpl
		");
    $mailtpl = $mailtplQ->execute();
    return $mailtpl;
  }

  //Get the published landingpages
  function getLandingpages() {
    $landingpageQ = One_Repository::selectQuery("landingpage");
    $landingpageQ->setRaw("
			select * from #__landingpage where published = 1 order by name asc
		");
    $pages = $landingpageQ->execute();
    return $pages;
  }

  public function slugify($string) {
    //remove any '-' from the string they will be used as concatonater
    $str = str_replace('-', ' ', $string);

    $str = htmlentities(utf8_decode($str));
    $str = preg_replace(
            array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/', '/&(.)[^;]*;/'), array('ss', "$1", "$1" . 'e', "$1"), $str);

    // remove any duplicate whitespace, and ensure all characters are alphanumeric
    $str = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $str);

    // lowercase and trim
    $str = trim(strtolower($str));
    //die;
    return $str;
  }

  //Get all actiontypes
  function getActionTypes() {
    $actiontypesQ = One_Repository::selectQuery("scenariostepactiontype");
    $actiontypesQ->setRaw("
			select * from #__scenariostepactiontype where published = 1
		");
    $actiontypes = $actiontypesQ->execute();
    return $actiontypes;
  }

  //Get all emails
  function getEmails() {
    $emailQ = One_Repository::selectQuery("email");
    $emailQ->setRaw("
			select * from #__mail where published = 1
		");
    $emails = $emailQ->execute();
    return $emails;
  }

  //Get all stories
  function getStories() {
    $storyQ = One_Repository::selectQuery("message");
    $storyQ->setRaw("
			select * from #__gmessage where published = 1
		");
    $stories = $storyQ->execute();
    return $stories;
  }

  //Get all jUsers
  function getUsers() {
    $uQ = One_Repository::selectQuery("juser");
    $uQ->setRaw("
			select * from #__users
		");
    $users = $uQ->execute();
    return $users;
  }

//Get all stories
  function getAvatars() {
    $aQ = One_Repository::selectQuery("avatar");
    $aQ->setRaw("
			select * from #__avatar where published = 1
		");
    $avatars = $aQ->execute();
    return $avatars;
  }

  function getScenarios() {
    $aQ = One_Repository::selectQuery("scenario");
    $aQ->setRaw("
			select * from #__scenario
		");
    $scenarios = $aQ->execute();
    return $scenarios;
  }

  function getScenarioSteps($scenario_id) {
    $aQ = One_Repository::selectQuery("scenariostep");
    $aQ->setRaw("
			select * from #__scenariostep WHERE scenario_id = $scenario_id order by step asc
		");
    $scenariosteps = $aQ->execute();
    return $scenariosteps;
  }

  public function translateHours($hours) {
    $translations = array(
        1 => "hour(s)",
        24 => "day(s)",
        168 => "week(s)",
        730 => "month(s)",
    );
    return $translations[$hours];
  }

  public function makeArrayOfIds($model) {
    $ids = array();
    foreach ($model as $m) {
      array_push($ids, $m["id"]);
    }
    return $ids;
  }

  public function wysiwyg($content, $fieldname) {
    $editor = & JFactory::getEditor('jce');
    $editor_params = "";
    return $editor->display($fieldname, $content, '100%', '400', '70', '15', 'false', $editor_params);
  }

  public function getMessageGroups() {
    $groupQ = One_Repository::selectQuery('messagegroup');
    $groups = $groupQ->execute();

    return $groups;
  }

  public function getImpactTypes() {
    $groupQ = One_Repository::selectQuery('impacttype');
    $groups = $groupQ->execute();

    return $groups;
  }

  public function getModels($schemeName, $published = true, $publishField = 'published') {

    $query = One_Repository::selectQuery($schemeName);

    if ($published)
      $query->where($publishField, 'eq', 1);

    $models = $query->execute();

    return $models;
  }

  public function getBadgeLanguageContent($badge) {

    $badgecontents = $badge->badgecontents;


    $languagecontent = array();

    foreach ($badgecontents as $badgecontent) {
      $languagecontent[$badgecontent->language] = $badgecontent;
    }

    return $languagecontent;
  }

  public function showDelta($datetime) {
    $delta = date_diff(date_create(), date_create($datetime));
    return $delta->format( $delta->invert ? "%a days, %H hours, %i minutes and %s seconds ago" : "in %a days, %H hours, %i minutes and %s seconds");
  }

  public function showDeltaShort($datetime) {
    $delta = date_diff(date_create(), date_create($datetime));
    return $delta->format( $delta->invert ? "%a d, %H:%i:%s ago" : "in %a d, %H:%i:%s");
  }

  
  /** helper for dump **/
  
  public function setDump($type,$id) {
    $GLOBALS['__dump__']['type'] = $type;
    $GLOBALS['__dump__']['id'] = $id;
  }
  public function setDumpDetail($key,$value) {
    $GLOBALS['__dump__'][$key] = $value;
  }
  public function getDump($thing) {
    return $GLOBALS['__dump__'][$thing];
  }
}