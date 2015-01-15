<?php
//------------------------------------------------------------------
// package db : functions to access the database
//------------------------------------------------------------------

	class One_Script_Package_Db extends One_Script_Package
	{

		public function loadAssocList( $sql, $var1, $var2 )
		{
			$result = mysql_query($sql);

			while($row = mysql_fetch_array($result)){
				$my_array[$row[$var1]] = $row[$var2];
				}

			return $my_array;
		}
	}
