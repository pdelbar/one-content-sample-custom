<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val;param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Jqtabs extends One_Script_Node_Abstract
{
	function execute(&$data, &$parent)
	{
		$params = array();
		$rawParams = explode(';', $this->data);
		foreach($rawParams as $rawParam)
		{
			$param = explode('=', $rawParam, 2);
			$params[$param[0]] = $param[1];
		}

		$cssTheme = 'ui-lightness';
		if(isset($params['theme']) && file_exists(One_Vendor::getInstance()->getFilePath().'/jquery/css/'.trim(strtolower($params['theme'])))) {
			$cssTheme = trim(strtolower($params['theme']));
		}

		$doc = JFactory::getDocument();
		$doc->addScript(One_Vendor::getInstance()->getSitePath().'/jquery/js/jquery-1.5.2.min.js', 'text/javascript' );
		$doc->addScript(One_Vendor::getInstance()->getSitePath().'/jquery/js/jquery-ui-1.8.14.custom.min.js', 'text/javascript' );
		$doc->addStyleSheet(One_Vendor::getInstance()->getSitePath().'/jquery/css/'.$cssTheme.'/jquery-ui-1.8.14.custom.css', 'text/css' );

		$id = md5($params['id'].microtime(true));

		$js = 'jQuery.noConflict();';
		$doc->addScriptDeclaration($js, 'text/javascript');

		$prevTabs = $data['tabTitles'];
		$data['tabTitles'] = array();

		// execute the rest of the chain first so the titles can be set
		$innertabs = $this->executeChain($this->chain, $data, $parent, $this);

		$content  = '<div id="'.$id.'">';
		$content .= '<ul>';
		foreach($data['tabTitles'] as $tId => $tabData)
		{
			if(true === $tabData['ajax'])
			{
				$link = $tabData['link'];
				if('/' == substr($link, 0, 1)) {
					$link = JURI::base(true).$link;
				}
				$uri = $link;
			}
			else {
				$uri = '#'.$tId;
			}
			$content .= '<li><a href="'.$uri.'">'.$tabData['title'].'</a></li>';
		}
		$content .= '</ul>';
		$content .= $innertabs;
		$content .= '</div>';

		$content .= '
<script type="text/javascript">
	jQuery(function() {
		jQuery("#'.$id.'").tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						"Error loading data" );
				}
			}
		});
	});
</script>
		';

		$data['tabTitles'] = $prevTabs;

		return $content;
	}
}
