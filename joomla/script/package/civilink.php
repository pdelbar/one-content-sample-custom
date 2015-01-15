<?php

  class One_Script_Package_CiviLink extends One_Script_Package
  {

    static $root;
    static $juseridfield = 'custom_9';
    static $wwwAdoption = 5;
    static $Jpref = 'fgmkz_';
    static $addresskeys = array('street_address', 'city', 'country', 'postal_code');
    protected static $instance;

    protected function __construct()
    {
      self::initialise();
    }

    public static function getInstance()
    {
      if (!isset(self::$instance)) {
        self::$instance = new One_Script_Package_CiviLink();
      }
      return self::$instance;
    }

    protected static function initialise($platform = 'joomla')
    {
      global $config;
      if (!isset($config)) {
        if ($platform == 'joomla') {
          $civipath   = 'administrator/components/com_civicrm/';
          self::$root = constant('JPATH_SITE') . '/';
        } else {
          self::$root = str_replace('delius', '', str_replace(__CLASS__, '', dirname(__FILE__)));
          $civipath   = '';
        }
        //echo '<BR>'.self::$root.$civipath.'civicrm.settings.php';
        require_once self::$root . $civipath . 'civicrm.settings.php';
        require_once 'CRM/Core/Config.php';
        $config = & CRM_Core_Config::singleton();
        require_once 'api/api.php';
      }
    }

    static function api($entity, $action, $data, $exit = true)
    {
      self::getInstance();
      $data['version'] = 3;
      $data['debug']   = 1;
      $e               = civicrm_api($entity, $action, $data);
      if ($e['is_error']) {
        echo '<div style="border: 1px solid #ccc; padding: 2px; background-color: #ff;">';
        echo '<B>entity: ', $entity, '<br/>action :' . $action, '</b>';
        echo '<pre>';
        var_dump($e);
        echo '<br/>';
        var_dump($data);
        echo '</pre></div>';
        if ($exit) {
//        throw new Exception('error :' . $e['error_message']);
        }
      }
      return $e;
    }

    static function result($result, $data = null)
    {
      if ($result['count'] == 1) {
        return $result['values'][$result['id']];
      } elseif ($result['count'] == 0) {
        return null;
      } else {
        echo '<hr>';
        if ($data) {
          var_dump($data);
          echo '<hr>';
        }
        var_dump($result);
        throw new Exception('More than 1 result :' . $result['count']);
      }
    }

    public static function getContact($juid)
    {
      $data = array(self::$juseridfield => $juid, 'api.CustomValue.get' => 1,);
      $res  = self::api('contact', 'get', $data);
//    print_r($contact);
      return (object)self::result($res, $data);
    }


    public static function getContactById($contactId)
    {
      if ($_REQUEST['test']) echo "<hr>getContactById($contactId)";
      $data = array('id' => $contactId, 'api.CustomValue.get' => 1);
      $res  = self::api('contact', 'get', $data);
      return (object)self::result($res, $data);
    }

    public static function getJUserId($contactId)
    {
      if ($_REQUEST['test']) echo "<hr>getJUserId($contactId)";
      $contact      = self::getContactById($contactId);
      $customFields = 'api.CustomValue.get';
      $customFields = $contact->$customFields;

      if ($_REQUEST['test']) print_r($customFields['values']);
      foreach ($customFields['values'] as $customValues) {
        if (isset($customValues['id']) && intval($customValues['id']) == 9) {
          return $customValues[0];
        }
      }

      return null;
    }

    public static function getContactId($juid)
    {
      try {
        $data = array(self::$juseridfield => $juid, 'return' => 'id');
        $res  = self::result(self::api('contact', 'get', $data), $data);
        if (is_null($res)) {
          //throw new Exception('No civicontact found for :'.$juid);
          return null;
        }
        return $res['id'];
      } catch (Exception $e) {
        print_r($e);
      }
    }

    /*
      juid is required
      $params=array('juid'=>$juid,
      'contact'=>array('first_name'=>'', 'last_name',...),
      'email'=>$email,
      'address'=>array('street_address'=>'', 'city'=>, 'postal_code'=>'', 'country'=>'')
      )

     */

    public static function updateContact($params)
    {
      if (array_key_exists('juid', $params)) {
        $contact = self::getContact($params['juid']);
      }
      if (!isset($contact)) {
        //var_dump($params);
        //throw new Exception('Contact not found');
      }
      if (array_key_exists('contact', $params)) {
        if (!isset($contact)) {
          $contact = (object)self::result(self::api('contact', 'create', array_merge(array('contact_type' => 'Individual', 'contact_sub_type' => 'Donor'), $params['contact'])));
        } else {
          $contact = (object)self::result(self::api('contact', 'update', array_merge(array('id' => $contact->id), $params['contact'])));
        }
      }

      if (array_key_exists('email', $params)) {
        $newemail = $params['email'];
        if ($contact->email != $newemail) {
          $res = self::api('email', 'get', array('contact_id' => $contact->id, 'email' => $contact->email, 'return' => 'id'));
          if ($res['count'] > 0) {
            $res = self::api('email', 'update', array('id' => $res['id'], 'is_primary' => 0, 'on_hold' => 1));
          }
          $res = self::api('email', 'create', array('contact_id' => $contact->id, 'email' => $newemail, 'is_primary' => 1, 'location_type_id' => 1));
        }
      }
      if (array_key_exists('address', $params)) {
        $oldaddr = self::api('address', 'get', array('contact_id' => $contact->id, 'is_primary' => 1));
        if ($oldaddr['count'] > 0) {
          $res = self::api('address', 'update', array('id' => $oldaddr['id'], 'is_primary' => 0, 'is_billing' => 0));
        }
        $adr     = $params['address'];
        $newaddr = self::api('address', 'create', array_merge(array('contact_id' => $contact->id, 'is_primary' => 1, 'is_billing' => 1), $adr));
      }
    }

    public static function filter($keys, $ar)
    {
      $result = array();
      $arkeys = array_keys($ar);
      foreach ($keys as $key) {
        if (in_array($key, $arkeys)) {
          $result[$key] = $ar[$key];
        }
      }
      return $result;
    }


    public static function &getContribution($contrib_id)
    {
      $params = array('id' => $contrib_id);
      $res    = self::api('contribution', 'getsingle', $params);
      self::fixPurpose($res);
      // also retrieve contribution page id
      $params['return']            = 'contribution_page_id';
      $res2                        = self::api('contribution', 'getsingle', $params);
      $res['contribution_page_id'] = $res2['contribution_page_id'];
      return $res;
    }


    //$options=array('sort'=>'receive_date DESC', 'limit'=>3);
    public static function &getContributions($juid, $recur_contribution_id = null, $options = null)
    {

      if (is_null($contactId = self::getContactId($juid)))
        return array();

      $params = array(
        'contact_id'             => $contactId,
        'contribution_status_id' => 1,
      );
      if (isset($options)) {
        $params['options'] = $options;
      }
      if (isset($recur_contribution_id)) {
        $params['contribution_recur_id'] = $recur_contribution_id;
      }

      $res     = self::api('contribution', 'get', $params);
      $results = array();
      if ($res['count'] > 0) {
        $values = $res['values'];
        foreach ($values as $id => $rec) {
//        echo '<pre>';
//        print_r($rec);
//        echo '</pre>';
          self::fixPurpose($rec);
          $results[] = (object)$rec;
        }
      }
      return $results;
    }

    private static function fixPurpose(&$contrib)
    {
      switch (trim($contrib['contribution_type'])) {
        case 'wwwAdoption' :
        case 'Member Dues' :
          $contrib['contribution_type'] = 'HeroRat adoption';
          break;
        default :
      }
    }

    public static function &getContributionsFromRcontrib($recur_contribution_id)
    {
      $params = array();
      if (isset($recur_contribution_id)) {
        $params['contribution_recur_id'] = $recur_contribution_id;
      }

      $res     = self::api('contribution', 'get', $params);
      $results = array();
      if ($res['count'] > 0) {
        $values = $res['values'];
        foreach ($values as $id => $rec) {
          self::fixPurpose($rec);
          $results[] = (object)$rec;
        }
      }
      return $results;
    }

    public static function &getRecurContributions($juid, $nr = null)
    {
      if (is_null($contactId = self::getContactId($juid)))
        return array();
      return self::getRecurContributionsFromContact($contactId, $nr);
    }

    public static function &getRecurContributionsFromContact($contactId, $nr = null)
    {
      $params = array('contact_id' => $contactId); //, 'contribution_type_id'=>self::$wwwAdoption
      if (isset($nr)) {
        $params['options'] = array('limit' => $nr);
      }
      $res     = self::api('contribution_recur', 'get', $params);
      $results = array();
      if ($res['count'] > 0) {
        $values = $res['values'];
        foreach ($values as $id => $rec) {
          $results[] = (object)$rec;
        }
      }
      return $results;
    }

    public static function getRecurContribution($id)
    {
      $params = array('id' => $id);
      $res    = self::api('contribution_recur', 'getsingle', $params);
      return $res;
    }

    public static function getMembershipFromRcontrib($rcontribid)
    {
      $params = array('contribution_recur_id' => $rcontribid);
      $res    = self::api('membership', 'get', $params);
      if ($res['count'] > 0) {
        $values = $res['values'];
        return $values[$res['id']];
      }
      return null;
    }


    public static function getMemberships($juid, $nr = null)
    {

      if (is_null($contactId = self::getContactId($juid)))
        return array();

      return self::getMembershipsForContact($contactId);
    }

    public static function getMembershipsForContact($cid, $nr = null)
    {

      $params = array('contact_id' => $cid);
      if (isset($nr)) {
        $params['options'] = array('limit' => $nr);
      }
      $res     = self::api('membership', 'get', $params);
      $results = array();
      if ($res['count'] > 0) {
        $values = $res['values'];
        foreach ($values as $id => $rec) {
          self::completeHerorat($rec);
          $results[] = (object)$rec;
        }
      }
      return $results;
    }

    public static function getHerorat($membership_id)
    {
      $params  = array(
        'id' => $membership_id,
      );
      $herorat = self::api('membership', 'getsingle', $params);

      self::completeHerorat($herorat);
      return $herorat;
    }

    /**
     * Shoudl retrieve memberships based on the custom_12_id field, but the API has an
     * issue with this, so we use a DAO query for now
     *
     * @param type $juid
     * @return type
     */
    public static function getHerorats($juid)
    {


      if (is_null($contactId = self::getContactId($juid))) {
        return array();
      }

      $results = array();
      /*
      $params = array(
          'contact_id' => $contactId,
      );
      $res = self::api('membership', 'get', $params);
      if ($res['count'] > 0) {
        $values = $res['values'];
        foreach ($values as $id => $herorat) {
          // use only active HeroRats
          if ($herorat['status_id'] == 2) {
            self::completeHerorat($herorat);
            $results[] = $herorat;
          }
        }
      }
  //        echo '<pre>';print_r($results);echo '</pre>';
      return $results;
        */

      $sql = "
      SELECT
        m.id as id
      FROM 
        civicrm_membership m
        JOIN civicrm_membership_status s on s.id = m.status_id
        LEFT JOIN civicrm_value_herorat_adoption_custom_fields_3 c ON c.entity_id= m.id
      WHERE 
        c.beneficiary_12 = $contactId
        AND
        s.is_current_member = 1 
      ";
      $dao = CRM_Core_DAO::executeQuery($sql);
      while ($dao->fetch()) {
        $herorat = self::api('membership', 'getsingle', array('version' => 3, 'id' => $dao->id));
        self::completeHerorat($herorat);
        $results[$herorat['id']] = $herorat;
      }

      return $results;
    }

    private static function completeHerorat(&$herorat)
    {
      $statusid             = $herorat['status_id'];
      $st                   = civicrm_api('membership_status', 'getsingle', array('version' => 3, 'id' => $statusid));
      $herorat['is_active'] = $st['is_current_member'];

      $herorat['herorat_name']             = $herorat['custom_6'];
      $herorat['herorat_points']           = $herorat['custom_16'];
      $herorat['herorat_points_threshold'] = $herorat['custom_17'];
      $herorat['herorat_stage']            = $herorat['custom_15'];
      $herorat['herorat_avatar']           = $herorat['custom_13'];
      if ($herorat['herorat_avatar'] == '')
        $herorat['herorat_avatar'] = 'adult1';
      $herorat['herorat_avatar_mood'] = $herorat['custom_14'];
      if ($herorat['herorat_avatar_mood'] == '')
        $herorat['herorat_avatar_mood'] = 'normal';
      $herorat['herorat_project']    = $herorat['custom_8'];
      $herorat['herorat_field']      = $herorat['custom_7'];
      $herorat['herorat_avatar_url'] = oneScriptPackageJoomla::livesite() . 'images/avatar/' . $herorat['herorat_avatar'] . '/' . ($herorat['herorat_field'] ? strtolower($herorat['herorat_field']) . '/' : '') . $herorat['herorat_avatar_mood'] . '.png';
      $herorat['herorat_color']      = $herorat['custom_19'];

      // load donor if needed
      if ($herorat['custom_23_id'] != $herorat['custom_12_id']) {
        $giver = self::api('contact', 'getsingle', array('version' => 3, 'id' => $herorat['custom_23_id']));
//      $herorat['herorat_donor'] = $giver['display_name'];   // Mr. Paul Delbar
        $herorat['herorat_donor'] = trim($giver['first_name'] . ' ' . $giver['last_name']);

      }

      switch ($herorat['herorat_stage']) {
        case 'converting' :
          $herorat['herorat_shortname'] = 'Converting';
          break;
        case 'baby' :
          $herorat['herorat_shortname'] = 'Baby rat';
          break;
        case 'training' :
          $herorat['herorat_shortname'] = 'In training';
          if ($herorat['herorat_field'] != '') {
            switch ($herorat['herorat_field']) {
              case 'MA' :
                $herorat['herorat_shortname'] = 'In mine detection training';
                break;
              case 'TB' :
                $herorat['herorat_shortname'] = 'In tuberculosis detection training';
                break;
            }
          }
          break;
        case 'field' :
          $herorat['herorat_shortname'] = 'In the field';
          if ($herorat['herorat_field'] != '') {
            switch ($herorat['herorat_field']) {
              case 'MA' :
                $herorat['herorat_shortname'] = 'Mine action';
                break;
              case 'TB' :
                $herorat['herorat_shortname'] = 'Tuberculosis detection';
                break;
            }
          }
          if ($herorat['herorat_project'] != '') {
            $c = One_Repository::selectOne('country', $herorat['herorat_project']);
            $herorat['herorat_shortname'] .= ' in ' . $c->name_en;
          }
          break;
      }
    }

    public function setHeroratMood($id, $cid, $mood)
    {
      $options = array('custom_14' => $mood);
      self::updateHerorat($id, $cid, $options);
    }

    public function setHeroratAvatar($id, $cid, $avatar)
    {
      $options = array('custom_13' => $avatar);
      self::updateHerorat($id, $cid, $options);
    }

    public function updateHerorat($id, $cid, $options)
    {
      $options['version']    = 3;
      $options['id']         = $id;
      $options['contact_id'] = $cid;
      //print_r($options);
      //die;
      return self::api('Membership', 'create', $options);
    }

    public function setStage($id, $stage)
    {
      $options['version']   = 3;
      $options['id']        = $id;
      $options['custom_15'] = $stage;
      //print_r($options);
      //die;
      $r = self::api('Membership', 'create', $options);
//    print_r($r);
      return;
    }

    public function setDomain($id, $domain)
    {
      $options['version']  = 3;
      $options['id']       = $id;
      $options['custom_7'] = $domain;
      //print_r($options);
      //die;
      return self::api('Membership', 'create', $options);
    }

    public function setProject($id, $project_id)
    {
      $options['version']  = 3;
      $options['id']       = $id;
      $options['custom_8'] = $project_id;
      //print_r($options);
      //die;
      return self::api('Membership', 'create', $options);
    }

    public function setGamePoints($id, $pts)
    {
      $options['version']   = 3;
      $options['id']        = $id;
      $options['custom_16'] = $pts;
      return self::api('Membership', 'create', $options);
    }

    public static function getMessages($juid, $typearr = null, $nr = null)
    {

    }
  }