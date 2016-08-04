<?php
require_once ('config.php');

$json = json_decode (file_get_contents("http://saleh-khazaei.com/jashn91/fetchReqs.php"),true);
$response = array();
$count = 0 ;
foreach ( $json as $key => $value )
{
    $j = json_decode(substr($value,0,-5),true);
    if ( isset( $j['data'] ))
    {
		try {
            $canFetch = false ;
            if (strpos(strtolower($j['req']), 'select') !== false) {
                $canFetch = true ;
            }
            
            $statement = $GLOBALS ['DBH']->prepare ( $j['req'] );
            $statement->execute ( $j['data'] );
            
            $json = array();
            if ( $canFetch )
            {
                while ( $row = $statement->fetch() )
                {
                    array_push ( $json , $row );
                }
            }
            $response [ $key ] = json_myencode($json);
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
    }else
    {
		try {
            $canFetch = false ;
            if (strpos(strtolower($j['req']), 'select') !== false) {
                $canFetch = true ;
            }

			$rows = $GLOBALS ['DBH']->query ( $j['req'] );
            
            $json = array();
            
            if ( $canFetch )
            {
                while ( $row = $rows->fetch() )
                {
                    array_push ( $json , $row );
                }
            }
            $response [ $key ] = json_myencode($json);
		}
		catch ( PDOException $e ) {
			echo $e->getMessage ();
			file_put_contents ( 'PDOErrors.txt', $e->getMessage (), FILE_APPEND );
		}
    }
    $count ++ ;
}
$url = 'http://saleh-khazaei.com/jashn91/doneReqs.php';
$data = array('resp' => json_myencode($response));

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }
echo $count . " " . trim($result);
?>