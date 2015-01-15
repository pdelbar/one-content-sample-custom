<?php
/**
 * One_Script_Content_Store_Joomla reads content items from a Joomla database
 */
class One_Script_Content_Store_Joomla extends One_Script_Content_Store
{
	public static function loadNamespace( $namespace )
	{
		$db = JFactory::getDBO();
		if ($db) {
			if (One_Script_Config::$nsLanguagePackage) {
				$lang = One_Script_Package::call( One_Script_Config::$nsLanguagePackage, 'getLanguage', '' );
			}

			$sql = 'SELECT * FROM #__nscontent WHERE ns  = ' . $db->Quote( $namespace );
			if ($lang) $sql .= ' AND lang = ' . $db->Quote( $lang );

//						echo $sql . '<br />';
			$db->setQuery( $sql );
			$result = $db->loadAssocList();
			if ( count( $db->getErrors() ) > 0 )
			{
				die( implode( '<br />', $db->getErrors() ) );
			}
			$any = false;

			if( is_array( $result ) )
			{
				foreach( $result as $row )
				{
//					print_r($row);
					$ns = new One_Script();
					$ns->parseString( '{section ' . $row['section'] . '}'.$row['content'].'{endsection}' );
//					$ns->dump();
					One_Script_Content_Factory::$nsContentCache[ $namespace ][ $row['section'] ] = $ns->rootNode->chain[0];
					$any = true;
				}
			}
		}
		return $any;
	}
}
