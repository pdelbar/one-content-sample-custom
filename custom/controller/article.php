<?php

class One_Controller_Article extends One_Controller {

  public function __construct(array $options = array()) {
    parent::__construct($options);
  }

  // Save landingpage
  public function execute_Save(array $options = array()) {

    $id = intval($this->options['id']);

    // Get the posted values
    $values = JRequest::getVar('articleForm', NULL, 'post');
    die(print_r($values));

	// Check if new or edit
    if ($id > 0) {
      $ob = One_Repository::selectOne('article', $id);
    } else {
      $ob = One::make('article');
    }

    // Fill in the values
    $ob->title = trim($values['title']);
    $ob->state = intval($values['state']);
    if ($id == 0)
      	$ob->created = date('Y-m-d H:m:s');

    // Update or insert
    if ($id > 0) {
      $ob->update();
    } else {
      $ob->insert();
    }

    // Redirect
    $redirect_url = JRoute::_("index.php?Itemid=" . intval($values['itemid']));
    header('Location: ' . $redirect_url);
  }

}
