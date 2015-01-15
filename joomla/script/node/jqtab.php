<?php
//-------------------------------------------------------------------------------------------------
// params has the form param=val;param=val
//-------------------------------------------------------------------------------------------------

class One_Script_Node_Jqtab extends One_Script_Node_Abstract
{
	function execute(&$data, &$parent)
	{
		$params = array();
		$rawParams = explode(';', html_entity_decode($this->data));
		foreach($rawParams as $rawParam)
		{
			$param = explode('=', $rawParam, 2);

			// little trick for now if you want to do something dynamic in the params
			$paramValue = str_replace(array('[[', ']]'), array('{', '}'), $param[1]);
			$ns = new One_Script();
			$paramValue = $ns->executeString($paramValue);

			$params[$param[0]] = $paramValue;
		}

		$id = md5($params['id'].microtime(true));

		$content  = '<div id="'.$id.'">';
		$content .= $this->executeChain($this->chain, $data, $parent);
		$content .= '</div>';

		$data['tabTitles'][$id] = array();
		$data['tabTitles'][$id]['title'] = (isset($params['title'])) ? $params['title'] : $params['id'];
		if(isset($params['link']))
		{
			$data['tabTitles'][$id]['ajax'] = true;
			$data['tabTitles'][$id]['link'] = $params['link'];
		}
		else {
			$data['tabTitles'][$id]['ajax'] = false;
		}

		return $content;
	}
}
