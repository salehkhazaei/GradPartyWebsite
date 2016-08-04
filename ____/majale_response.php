<?php
	require_once('config.php');
    if ( isset ( $_POST['left'] ) )
    {
        $rows = Select ( "*", $GLOBALS['tbl_request'], "id=?", array ($_POST['left']) );
        if ( $row = $rows->fetch() ) 
        {
            if ( isset ( $_FILES["file"] ) && $_FILES['file']['error'] != 4 )
            {
                $target_dir = "magazine/";
                $target_file = $target_dir .$row['_userid']."_".abs(((int)$row['_userid'] * 11) % 9999)."-left.jpg";
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["file"]["tmp_name"]);
                    if($check !== false) {
                        $uploadOk = 1;
                    } else {
                        $uploadOk = 0;
                    }
                }	
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $uploadOk = 0;
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    CloseDatabase();
                    die();
                }			
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    CloseDatabase();
                    die();
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                        Update ( $GLOBALS['tbl_request'], "_done=_done+1", "id=?", array ( $_POST['left'] ) );
                        Update ( $GLOBALS['tbl_users'], "_error=?", "id=?", array ( $_POST['error'], $row['_userid'] ) );
                        Update ( $GLOBALS['tbl_users'], "_left=?", "id=?", array ( $_POST['isleft'], $row['_userid'] ) );
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                        CloseDatabase();
                        die();
                    }
                }
            }
        }
    }
    if ( isset ( $_POST['right'] ) )
    {
        $rows = Select ( "*", $GLOBALS['tbl_request'], "id=?", array ($_POST['right']) );
        if ( $row = $rows->fetch() ) 
        {
            if ( isset ( $_FILES["file"] ) && $_FILES['file']['error'] != 4 )
            {
                $target_dir = "magazine/";
                $target_file = $target_dir .$row['_userid']."_".abs(((int)$row['_userid'] * 11) % 9999)."-right.jpg";
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["file"]["tmp_name"]);
                    if($check !== false) {
                        $uploadOk = 1;
                    } else {
                        $uploadOk = 0;
                    }
                }	
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $uploadOk = 0;
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    CloseDatabase();
                    die();
                }			
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    CloseDatabase();
                    die();
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                        Update ( $GLOBALS['tbl_request'], "_done=_done+1", "id=?", array ( $_POST['right'] ) );
                        Update ( $GLOBALS['tbl_users'], "_error=?", "id=?", array ( $_POST['error'], $row['_userid'] ) );
                        Update ( $GLOBALS['tbl_users'], "_left=?", "id=?", array ( $_POST['isleft'], $row['_userid'] ) );
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                        CloseDatabase();
                        die();
                    }
                }
            }
        }
    }
	CloseDatabase();
?>