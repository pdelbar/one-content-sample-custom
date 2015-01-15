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
    
	// Check if new or edit
    if ($id > 0) {
      $ob = One_Repository::selectOne('article', $id);
      $image = array('image' => strval($ob->image));
    } else {
      $ob = One_Repository::getInstance('article');
      $image = array('image' => '');
    }

    // Fill in the values
    $ob->name = trim($values['name']);
    $ob->published = intval($values['published']);
    $ob->video = trim($values['video']);
    if ($id == 0)
      	$ob->date_created = date('Y-m-d H:m:s');
    $ob->date_updated = date('Y-m-d H:m:s');
    
    //create accompanying image and return image path
    $imageParams = array('width' => 300, 'height' => 0);
    $uploadedimages = oneScriptPackageImageupload::getUploadedImages('/images/games/news/', $id, $image, $imageParams);

    if (array_key_exists('image', $uploadedimages)) {
      $ob->image = $uploadedimages['image'];
    } else {
      	if ($ob->image && isset($_POST['image']) && ($_POST['image'] == '')) {
        	$ob->image = '';
		}
    }

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

  
  // Remove message
  public function execute_Remove(array $options = array()) {
    // check to delete all content
    $id = intval($this->options['id']);
    $ob = One_Repository::selectOne('article', $id);
    $contents = $ob->getRelated('articlecontents');
    
    foreach ($contents as $content) {
      $content->delete();
    }
    
    $ob->delete();
    
    $itemId = intval(JRequest::getVar('Itemid', 0));
    
    $redirect_url = JRoute::_("index.php?Itemid=" . $itemId . '&task=list&view=list');
    header('Location: ' . $redirect_url);
  }

}
