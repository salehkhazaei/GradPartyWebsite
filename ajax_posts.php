<?php
	require_once('config.php');
	if ( ! Access() )
	{
		echo "{\"error\": \"please login\"}";
		CloseDatabase();
		die();
	}
	
	$rows = Select ( "*", $GLOBALS['tbl_users'], "id=?", array ($_SESSION['user']) );
	if ( $row = $rows->fetch() )
	{
		if ( isset ( $_GET['like'] ) )
		{
			$wrows = Select ( "*", $GLOBALS['tbl_likes'], "_postid=? AND _userid=?", array ($_GET['like'],$_SESSION['user']) );
			if ( $wrow = $wrows->fetch() )
			{
				Delete ( $GLOBALS['tbl_likes'], "_postid=? AND _userid=?", array ($_GET['like'],$_SESSION['user']) );
			}
			else
			{
				Create ( $GLOBALS['tbl_likes'], "(_postid,_userid)", "(?,?)", array ($_GET['like'],$_SESSION['user']) );
			}
		}
		if ( isset ( $_GET['comment'] ) )
		{
			$wrows = Select ( "*", $GLOBALS['tbl_posts'], "id=?", array ($_GET['comment']) );
			if ( $wrow = $wrows->fetch() )
			{
				$hashtags = "";
				preg_match_all("/(#[^\s,]+)/", $_POST ['text'], $matches);
				foreach ($matches[0] as $hashtag)
					$hashtags .= substr($hashtag,1).' ';
				
				$text = str_replace ( "&" , "&amp;" , $_POST['text'] );
				$text = str_replace ( "\"" , "&quot;" , $text );
				$text = str_replace ( "<" , "&lt;" , $text );
				$text = str_replace ( ">" , "&gt;" , $text );
				Create ( $GLOBALS['tbl_posts'], "(_parent,_group,_writer,_text,_time,_tags)", 
												"(?,?,?,?,NOW(),?)", 
												array ($_GET['comment'],
													$wrow['_group'],
													$_SESSION['user'],
													$text,
													$hashtags) );
				$id = lastInsertedID();
				$crows = Select ( "_name,_family" , $GLOBALS ['tbl_users'], "id=?", array ($_SESSION['user']) );
				if ( $crow = $crows->fetch() )
				{
					$writer = $crow['_name'].' '.$crow['_family'];
				}
				echo "{\"id\": \"".$id."\",\"writer\": \"".$writer."\"}";
				CloseDatabase();
				die();
			}
		}

		$count = 0 ;
		$conditions = "";
		$order = "DESC";
		$data = array ();
		if ( isset ( $_GET['count'] ) && $_GET['count'] == 1 )
		{
			$count = 1 ;
		}
		if ( isset ( $_GET['id'] ) )
		{
			$conditions .= "_writer=? ";
			array_push ( $data , $_GET['id'] );
		}
		if ( isset ( $_GET['group'] ) )
		{
			if ( strlen ( trim ( $conditions ) ) != 0 )
			{
				$conditions .= " AND ";
			}
			$conditions .= " _group=? ";
			array_push ( $data , $_GET['group'] );
		}
		if ( isset ( $_GET['tag'] ) )
		{
			if ( strlen ( trim ( $conditions ) ) != 0 )
			{
				$conditions .= " AND ";
			}
			$conditions .= " _tags LIKE ? ";
			array_push ( $data , "%".$_GET['tag']."%" );
		}
		if ( isset ( $_GET['parent'] ) )
		{
			if ( strlen ( trim ( $conditions ) ) != 0 )
			{
				$conditions .= " AND ";
			}
			$conditions .= " _parent=? ";
			$order = "ASC";
			array_push ( $data , $_GET['parent'] );
		}
		else
		{
			if ( strlen ( trim ( $conditions ) ) != 0 )
			{
				$conditions .= " AND ";
			}
			$conditions .= " _parent=-1 ";
		}
		if ( strlen ( trim ( $conditions ) ) != 0 )
		{
			$conditions .= " AND ";
		}
		$conditions .= " 1=1 ";
		$rows = Select ( ( $count == 1 ? "COUNT(id)" : "*" ) , $GLOBALS['tbl_posts'] , $conditions." ORDER BY id ".$order , $data);
		$json_str = '{"data": [';
		$q = false;
		while ( $row = $rows->fetch() )
		{
			$writer = $row['_writer'];
			$crows = Select ( "jashnid,_name,_family" , $GLOBALS ['tbl_users'], "id=?", array ($row['_writer']) );
			if ( $crow = $crows->fetch() )
			{
				$writer = $crow['_name'].' '.$crow['_family'];
                if ( $crow['jashnid'] != getJashnID() )
                {
                    continue;
                }
			}
            else 
            {
                continue;
            }
			$wrows = Select ( "id", $GLOBALS['tbl_likes'], "_postid=? AND _userid=?", array ($row['id'],$_SESSION['user']) );
			$liked = 0;
			if ( $wrow = $wrows->fetch() )
			{
				$liked = 1;
			}

			$wrows = Select ( "COUNT(id)", $GLOBALS['tbl_likes'], "_postid=?", array ($row['id']) );
			$likes = 0;
			if ( $wrow = $wrows->fetch() )
			{
				$likes = $wrow[0];
			}

			
			$hashtags = "";
			preg_match_all("/(#[^\s,]+)/", $row['_text'], $matches);
			foreach ($matches[0] as $hashtag)
				$hashtags .= substr($hashtag,1).' ';
	
			if ( $q == true )
			{
				$json_str .= ",";
			}
			$q = true ;

			$json_str .= 
'{
"id": "'.escapeJsonString($row['id']).'",
"group": "'.escapeJsonString($row['_group']).'",
"writerid": "'.escapeJsonString($row['_writer']).'",
"writer": "'.escapeJsonString($writer).'",
"text": "'.escapeJsonString($row['_text']).'",
"likes": "'.escapeJsonString($likes).'",
"liked": "'.escapeJsonString($liked).'",
"time": "'.escapeJsonString($row['_time']).'",
"mine": "'.escapeJsonString(($_SESSION['user'] == $row['_writer'] ? 1 : 0 )).'",
"tags": "'.escapeJsonString($hashtags).'"
}';
		}
		$json_str .= '] }';
		echo $json_str;
	}
	else{
		echo "{\"error\": \"invalid session\"}";
		CloseDatabase();
		die();
	}
	CloseDatabase();
?>