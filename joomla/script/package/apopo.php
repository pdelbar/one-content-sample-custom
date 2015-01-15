<?php
//------------------------------------------------------------------
// Pledge functionaliteit
//------------------------------------------------------------------

	class One_Script_Package_Apopo extends One_Script_Package
	{

    function getToken() {
      return JUtility::getToken();
    }
    
		function getLatestNews(){
			$newsQ = One_Repository::selectQuery('news');
			$newsQ->setLimit(3);
			$news = $newsQ->execute();

			return $news;
		}
		
		/* Get the topics */
		function getTopics(){

			$topicQ = One_Repository::selectQuery("topic");
			
			$lang =& JFactory::getLanguage();
			$language = $lang->getTag();
		
			switch($language)
			{
				case 'nl-NL':
					$query = "SELECT id, name_nl as name, shorttext_nl as shorttext, slidertext_nl as slidertext, url_nl as url, sliderimage FROM #__topic";
					break;
				case 'fr-FR':
					$query = "SELECT id, name_fr as name, shorttext_fr as shorttext, slidertext_fr as slidertext, url_fr as url, sliderimage FROM #__topic";
					break;
				case 'de-DE':
					$query = "SELECT id, name_de as name, shorttext_de as shorttext, slidertext_de as slidertext, url_de as url, sliderimage FROM #__topic";
					break;
				default:
					$query = "SELECT id, name_en as name, shorttext_en as shorttext, slidertext_en as slidertext, url_en as url, sliderimage FROM #__topic";
					break;
			}
			
			$topicQ->setRaw($query);
			$topics = $topicQ->execute();

			return $topics;
		}
		
		/* Get the countries */
		function getCountries($topic){

			$countryQ = One_Repository::selectQuery("country");
			
			$lang = self::getLanguage();
		
			$query = "
			  SELECT id, name_".$lang." as name, shorttext_".$lang." as shorttext, itemid_".$lang." as itemid, year, area, published, landmines, remnants, munition, keytitle_".$lang." as keytitle, menuimage
			  FROM #__country
			  WHERE topic_id = ".$topic."
			  ORDER BY rang ASC";
			
			$countryQ->setRaw($query);
			$countries = $countryQ->execute();

			return $countries;
		}
		
		/* Get the related news based on topic and country */
		function getRelatedNews($topic, $country, $amount = 1){
			$newsQ = One_Repository::selectQuery("news");
			$lng = self::getLanguage();
		
			$query = "select n.* from #__newsitem_repeat_country c inner join #__newsitem n on c.parent_id = n.id inner join #__newsitem_repeat_topic t on n.id = t.parent_id where c.country = ".$country." and published_".$lng." = 1 and t.topic = ".$topic." order by n.newsitemdate desc limit ".$amount;
			
			$newsQ->setRaw($query);
			$news = $newsQ->execute();

			return $news;
		}
		
		/* Get all news */
		function getNews($limit = 0, $offset = 0){
			$newsQ = One_Repository::selectQuery("news");
			$lng = self::getLanguage();
		
			if($offset > 0)
				$query = "select * from #__newsitem where published_".$lng." = 1 order by newsitemdate desc LIMIT " . $limit . ", " . $offset;
			else
				$query = "select * from #__newsitem where published_".$lng." = 1 order by newsitemdate desc";
			
			$newsQ->setRaw($query);
			$news = $newsQ->execute();

			return $news;
		}
		
		/* Get the people */
		function getPeople(){

			$peopleQ = One_Repository::selectQuery("team");
			$lang = self::getLanguage();
			
			$query = "select u.id, c.name, c.description_".$lang." as description, c.image, c.email, c.published, c.function_".$lang." as function, t.name_".$lang." as usertype from #__crew_repeat_usertype u inner join #__crew c on u.parent_id = c.id left join #__crewtype t on u.usertype = t.id where c.published = 1 order by u.usertype, c.rang asc";
			
			$peopleQ->setRaw($query);
			$people = $peopleQ->execute();

			return $people;
		}
		
		/* Get the reports */
		function getReports(){

			$reportsQ = One_Repository::selectQuery("report");
			$lang = self::getLanguage();
			
			$query = "select id, name_".$lang." as name, description_".$lang." as description, file, image, year from #__reports where published = 1 order by year desc";
			
			$reportsQ->setRaw($query);
			$reports = $reportsQ->execute();

			return $reports;
		}
		
		/* Get the information of a country based on the itemid */
		function getCountry(){

			$headerQ = One_Repository::selectQuery("country");
			$lang = self::getLanguage();
			$itemid = JRequest::getVar('Itemid');
			
			$query = "
			  SELECT id, name_".$lang." as name, shorttext_".$lang." as shorttext, itemid_".$lang." as itemid, year, area, landmines, remnants, munition, headerimg, topic_id, keytitle_".$lang." as keytitle, menuimage, published
			  FROM #__country
			  WHERE itemid_".$lang." = ".$itemid." LIMIT 1";
			
			$headerQ->setRaw($query);
			$header = $headerQ->execute();

			return $header;
		}
		
		function getOtherCountries($topic, $current){
			
			$countryQ = One_Repository::selectQuery("country");
			$lang = self::getLanguage();
			
			$query = "
				select c.id, c.name_".$lang." as name, c.shorttext_".$lang." as shorttext, c.itemid_".$lang." as itemid, c.year, c.area, c.landmines, c.remnants, c.munition, c.headerimg, c.topic_id, c.keytitle_".$lang." as keytitle, c.menuimage, t.url_en as topicitem, c.published
				from
				  #__country c
				  inner join #__topic t on c.topic_id = t.id
				where
				  c.topic_id = ".$topic."
				  and
				  c.id != ".$current."
				ORDER BY
				  RAND() LIMIT 2
			";
			$countryQ->setRaw($query);
			$countries = $countryQ->execute();

			return $countries;
		}
		
		/* Get the partners of a country based on the itemid */
		function getPartners(){

			$partnerQ = One_Repository::selectQuery("partner");
			$lang = self::getLanguage();
			$itemid = JRequest::getVar('Itemid');
			
			$query = "select c.id, p.id as partner_id, p.name, p.url, p.logo, p.description_".$lang." as description, p.showlogo from #__country co inner join #__partners_repeat_countries c on co.id = c.countries inner join #__partners p on c.parent_id = p.id where co.itemid_en = ".$itemid." and p.published = 1";
			
			$partnerQ->setRaw($query);
			$partners = $partnerQ->execute();

			return $partners;
		}
		
		/* Get the partners of a country based on the itemid */
		function getIndicators($country){

			$indicatorQ = One_Repository::selectQuery("indicator");
			$lang = self::getLanguage();
			
			$query = "select c.id, i.icon, value, text_".$lang." as text, url_".$lang." as url from #__country_13_repeat c inner join #__icon i on c.icon = i.id where parent_id = ".$country;
			
			$indicatorQ->setRaw($query);
			$indicators = $indicatorQ->execute();

			return $indicators;
		}
		
		/* Get the steps for a how page based on the category */
		function getHow(){

  $itemid = JRequest::getVar('Itemid');
  $howQ = One_Repository::selectQuery("how");
  $lang = self::getLanguage();

  $query = "
			    SELECT
			      h.id,
			      h.name_".$lang." as name,
			      h.intro_".$lang." as intro,
			      h.content_".$lang." as content,
			      h.image_large,
			      h.height,
			      hc.name as howname
			    FROM
			      #__howcategory hc
			      INNER JOIN #__how h on hc.id = h.subtopic
          WHERE
            hc.itemid_".$lang." = ".$itemid."
            AND
            published = 1
          ORDER BY
            rang ASC";

  $howQ->setRaw($query);
  $how = $howQ->execute();

  return $how;
  }

    function getAllHows(){

      $itemid = JRequest::getVar('Itemid');
      $howQ = One_Repository::selectQuery("howcategory");
      $lang = self::getLanguage();

      $query = "
			    SELECT
			      hc.id,
			      hc.name,
			      h.image_large,
            hc.itemid_".$lang." as itemid
			    FROM
			      #__howcategory hc
			      INNER JOIN #__how h on hc.id = h.subtopic and h.rang = 1
          WHERE
            published = 1
          ";

      $howQ->setRaw($query);
      $hows = $howQ->execute();
      $result = array();
      foreach ($hows as $how) {
        $result[ $how->id ] = $how;
      }

      return $result;
    }
		
		function getCountryStories($limit = 1, $itemid = '', $story_id = 0){

			if (trim($itemid == '')){
				$itemid = JRequest::getVar('Itemid');
			}
			$storyQ = One_Repository::selectQuery("story");
			$lang = self::getLanguage();
			
			$extra = "";
			if (intval($story_id) > 0){
				$extra = " and s.id = ".$story_id;
			}
			
			$query = "select s.id, s.name_".$lang." as name, s.intro_".$lang." as intro, s.story_".$lang." as story, s.image from #__country co inner join #__story_repeat_countries c on co.id = c.countries inner join #__story s on c.parent_id = s.id inner join #__story_repeat_topics t on t.parent_id = s.id where s.published = 1 and co.itemid_".$lang." = ".$itemid." and t.topics = co.topic_id".$extra." order by rand() LIMIT ".$limit;
			
			$storyQ->setRaw($query);
			$stories = $storyQ->execute();

			return $stories;
		}
		
		function getStoriesByTopic($topic, $limit){

			$storyQ = One_Repository::selectQuery("story");
			$lang = self::getLanguage();
			$extlimit = "";
			if (intval($limit) > 0){
				$extlimit = "LIMIT ".$limit;
			}
						
			$query = "select s.id, s.name_".$lang." as name, s.intro_".$lang." as intro, s.story_".$lang." as story, s.image from fgmkz_story_repeat_topics st inner join fgmkz_story s on st.parent_id = s.id where st.topics = ".$topic." and s.published = 1 order by RAND() ".$extlimit;
			
			$storyQ->setRaw($query);
			$stories = $storyQ->execute();

			return $stories;
		}
		
		function getStory($story_id){

			$storyQ = One_Repository::selectQuery("story");
			$lang = self::getLanguage();
						
			$query = "select s.id, s.name_".$lang." as name, s.intro_".$lang." as intro, s.story_".$lang." as story, s.image from #__story s where s.published = 1 and s.id = ".$story_id." order by rand() LIMIT 1";
			
			$storyQ->setRaw($query);
			$stories = $storyQ->execute();

			return $stories;
		}
		
		function getJobs(){

			$jobsQ = One_Repository::selectQuery("job");
						
			$query = "select * from #__jobs where published = 1 order by rang asc";
			
			$jobsQ->setRaw($query);
			$jobs = $jobsQ->execute();

			return $jobs;
		}
		
		function getRats(){

			$ratsQ = One_Repository::selectQuery("amount");
						
			$query = "SELECT id, name_en as name, description_en as description, image FROM #__amounts WHERE published = 1 AND type = 1 ORDER BY RAND() LIMIT 3";
			
			$ratsQ->setRaw($query);
			$rats = $ratsQ->execute();

			return $rats;
		}
		
		
				
		function getRelatedStories(){
			$itemid = JSite::getMenu()->getActive()->tree[2];
			return self::getCountryStories(100,$itemid);
		}
		
		/* Get the gallery of a country based on the itemid */
		function getGalleries(){

			$galleryQ = One_Repository::selectQuery("gallery");
			$lang = self::getLanguage();
			$itemid = JRequest::getVar('Itemid');
			
			$query = "
			select g.id, g.name_".$lang." as name, g.description_".$lang." as description, g.image, g.video, g.videotype, g.gallery, g.setid from #__country c 
			inner join #__gallery_repeat_countries gc on c.id = gc.countries 
			inner join #__gallery g on gc.parent_id = g.id 
			where g.publish = 1 and c.itemid_".$lang." = ".$itemid." ORDER BY g.rang DESC LIMIT 3
			";
			
			$galleryQ->setRaw($query);
			$galleries = $galleryQ->execute();

			return $galleries;
		}
		
		function getAllGalleries(){
			
			$galleryQ = One_Repository::selectQuery("gallery");
			$lang = self::getLanguage();
			
			$query = "
			select id, name_".$lang." as name, description_".$lang." as description, image, video, videotype, gallery, setid from #__gallery where publish = 1 order by rang desc
			";
			
			$galleryQ->setRaw($query);
			$galleries = $galleryQ->execute();

			return $galleries;
		}
		
		function getFeaturedGalleries(){
			
			$galleryQ = One_Repository::selectQuery("gallery");
			$lang = self::getLanguage();
			
			$query = "
			select id, name_".$lang." as name, description_".$lang." as description, image, video, videotype, gallery, setid from #__gallery where publish = 1 and featured = 1 order by rang asc limit 3
			";
			
			$galleryQ->setRaw($query);
			$galleries = $galleryQ->execute();

			return $galleries;
		}
		
		function getAboutGalleries(){
			
			$galleryQ = One_Repository::selectQuery("gallery");
			$lang = self::getLanguage();
			
			$query = "
			select g.id, g.name_".$lang." as name, g.description_".$lang." as description, g.image, g.video, g.videotype, g.gallery, g.setid from #__gallery_repeat_Topics gt inner join #__gallery g on gt.parent_id = g.id where gt.topics = 3 and g.publish = 1 order by g.id desc limit 3";
			
			$galleryQ->setRaw($query);
			$galleries = $galleryQ->execute();

			return $galleries;
		}
		
		function getGalleryByCountry($country){
			
			$galleryQ = One_Repository::selectQuery("gallery");
			$lang = self::getLanguage();
			
			$query = "
			select g.id, g.name_".$lang." as name, g.description_".$lang." as description, g.image, g.video, g.videotype, g.gallery, g.setid from #__gallery_repeat_countries gt inner join #__gallery g on gt.parent_id = g.id where gt.countries = ".$country." order by g.id desc limit 1
			";
			
			$galleryQ->setRaw($query);
			$galleries = $galleryQ->execute();

			return $galleries;
		}
		
		
		function getPublications(){
			
			$publicationQ = One_Repository::selectQuery("publication");
			$lang = self::getLanguage();
			
			$query = "
				select p.id + ROUND((RAND() * (1000000-0))+0) as id, p.name, p.description, p.published, p.file, p.url, p.pdate, p.notfree, t.topics from #__publications_repeat_topics t inner join #__publications p on t.parent_id = p.id order by topics asc, pdate desc
			";
			
			$publicationQ->setRaw($query);
			$publications = $publicationQ->execute();

			return $publications;
		}
		
		function getMedia(){
			
			$mediaQ = One_Repository::selectQuery("media");
			$lang = self::getLanguage();
			
			$query = "
				select * from #__media where published = 1 and language = '".$lang."' or (language = 'en' and featured = 1) order by datum desc
			";
			
			$mediaQ->setRaw($query);
			$media = $mediaQ->execute();

			return $media;
		}
		
		//get amounts by type (type 1 = adoption, type 2 = single gift)
		function getAmounts($type){
			
			$amountsQ = One_Repository::selectQuery("amount");
			$lang = self::getLanguage();
			
			$query = "
				SELECT name_".$lang." AS name, description_".$lang." AS decription, type, image, price FROM #__amounts WHERE published = 1 and type = ".$type." ORDER BY price ASC LIMIT 3
			";
			
			$amountsQ->setRaw($query);
			$amounts = $amountsQ->execute(false);

			return $amounts;
		}
		
		function getLanguage(){
			$lang =& JFactory::getLanguage();
			$language = $lang->getTag();
		
			switch($language)
			{
				case 'nl-NL':
					$lng = "nl";
					break;
				case 'fr-FR':
					$lng = "fr";
					break;
				case 'de-DE':
					$lng = "de";
					break;
				default:
					$lng = "en";
					break;
			}
			return $lng;
		}
		
		function returnTest() {
			return "test";
		}
		
		function popArray($currArray){
			array_pop($currArray);
			return $currArray;
		}
		
		function launchHeaderSlider(){
			One_Vendor::getInstance()
				->loadScript(JURI::base() . 'media/js/plugins/jquery.cycle2.min.js', 'head', 200)
				->loadScript(JURI::base() . 'media/js/custom.js', 'head', 210)
				->loadScriptDeclaration('headerSlider()', 'onload', 220);
		}		
		function launchTestimonialSlider(){
			One_Vendor::getInstance()
				->loadScript(JURI::base() . 'media/js/plugins/jquery.cycle2.min.js', 'head', 200)
				->loadScript(JURI::base() . 'media/js/custom.js', 'head', 210)
				->loadScriptDeclaration('testimonialSlider()', 'onload', 220);
		}
		
		function getHeaders(){
			$query = One_Repository::selectQuery('header');
			$query->where('published', 'eq', 1);
			$query->where('language', 'in', array(JFactory::getLanguage()->getTag()));
			$query->setOrder('sequence+');
			$model = $query->execute();
			return $model;

		}


    function getHome(){
      $q = One_Repository::selectQuery('homebanner');
      $q->where('published','eq',1);
      $q->where('starting','<=',date('Y-m-d H:i:s'));
      $q->where('ending','>=',date('Y-m-d H:i:s'));
      $q->setOrder('seq+');
      $banners = $q->execute();
//      $one = new stdClass;
//      $one->image = 'xmas14.jpg';
//      $one->url = 'https://www.apopo.org/en/support';
//      $one->news = 'left';
//      $one->darken = true;
//
//      $two = new stdClass;
//      $two->image = 'tb.jpg';
//      $one->url = 'https://www.apopo.org/en/tuberculosis-detection/projects';
//      $two->news = 'none';
//      $two->darken = true;

//      $home = array(
//        $one,
//        $two,
//      );
      return $banners;
    }

    function dumpUTM() {
      print_r($_SESSION[ 'UTMEM' ]);
    }
	}