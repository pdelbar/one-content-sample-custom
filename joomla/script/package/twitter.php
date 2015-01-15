<?php

  class oneScriptPackageTwitter extends One_Script_Package
  {
    function getCard($id)
    {
      $tc = One_Repository::selectOne('twittercard',$id);
      if ($tc) return $tc;

      $tc = One_Repository::getInstance('twittercard');
      $tc->id = $id;
      $tc->insert();
      $tc = One_Repository::selectOne('twittercard',$id);
      return $tc;
    }

    function getSkin($id)
    {
      $tc = One_Repository::selectOne('pageskin',$id);
      if ($tc) return $tc;

      $tc = One_Repository::getInstance('pageskin');
      $tc->id = $id;
      $tc->insert();
      $tc = One_Repository::selectOne('pageskin',$id);
      return $tc;
    }

  }