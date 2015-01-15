<?php
//------------------------------------------------------------------
// package request : functions to access the request
//------------------------------------------------------------------

	class oneScriptPackagePanel extends One_Script_Package
	{
		static $firstPanel = false;
		static $firstTab = false;

		function startPane($id)
		{
			return "<div id='$id' class='pane-sliders'>";
		}

		function startPanel($text, $id)
		{
				return "<div id='$id' class='panel'>
						<h3 id='$id-title' class='jpane-toggler title'><span>$text</span></h3>
						<div class='jpane-slider content'>";
		}

		function endPanel()
		{
			return "</div></div>";
		}

		function endPane()
		{
			if (!self::$firstPanel)
			{
				$script = "window.addEvent('domready', function(){
									new Accordion(  $$('.panel h3.jpane-toggler'),
													$$('.panel div.jpane-slider'),
													{
														'duration' : 200
													}
												);
											 });";

				$document = &JFactory::getDocument();

				$document->addStyleSheet('components/com_one/css/general.css');

				$document->addScript('includes/js/joomla.javascript.js');
				$document->addScript('media/system/js/mootools.js');

				$document->addScriptDeclaration( $script );

				self::$firstPanel = true;

			}

			return "</div>";
		}

		function openTabs($id)
		{
			return "<br clear='all' />\n<dl class='tabs' id='$id'>";
		}

		function startTab($text, $id)
		{
				return "<dt id='$id-title'>
						<span>$text</span></dt><dd>";
		}

		function endTab()
		{
			return "</dd>";
		}

		function closeTabs()
		{
			if (!self::$firstTab)
			{
				$script = "window.addEvent('domready', function()
								{
									$$('dl.tabs').each(function(tabs)
										{
											new JTabs(tabs, {});
										});
								});";

				$document = &JFactory::getDocument();

				$document->addStyleSheet('components/com_one/css/general.css');

				$document->addScript('includes/js/joomla.javascript.js');
				$document->addScript('media/system/js/mootools.js');
				$document->addScript('media/system/js/tabs.js');

				$document->addScriptDeclaration( $script );

				self::$firstTab = true;

			}

			return "</dl>";
		}

		function tabs()
		{
			//Inform Joomla we want to make use of panes
			jimport('joomla.html.pane');
			//Get JPaneTabs instance
			$myTabs = & JPane::getInstance('tabs', array('startOffset'=>0));
			$output = '';
			//Create Pane
			$output .= $myTabs->startPane( 'pane' );
			//Create 1st Tab
			$output .= $myTabs->startPanel( '1st', 'tab1' );
			$output  .= '<p>This is the first tab</p>';
			$output .= $myTabs->endPanel();
			//Create 2nd Tab
			$output .= $myTabs->startPanel( '2nd', 'tab2' );
			$output  .= '<p>This is the secondt tab</p>';
			$output .= $myTabs->endPanel();
			//Create 3rd Tab
			$output .= $myTabs->startPanel( '3rd', 'tab3' );
			$output  .= '<p>This is the third tab</p>';
			$output .= $myTabs->endPanel();
			//End Pane
			$output .= $myTabs->endPane();
			//Output to web
			echo $output;
		}
	}
