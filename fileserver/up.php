<html>
    <head>
    </head>
    <body>
<?php
	if ( isset ( $_FILES["file"] ) && $_FILES['file']['error'] != 4 )
	{
		$target_dir = "uploads/";
		$target_file = $target_dir . $_FILES["file"]["name"];
		$uploadOk = 1;
		// Check if image file is a actual image or fake image
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            echo "<h1>done</h1>";
        } else {
            echo "Sorry, there was an error uploading your file.";
            die();
        }
	}
    else{
        echo ":|";
    }
?>
        <form method='post' enctype="multipart/form-data">
            <div class='row'><div class='col-md-offset-8 col-md-2'><input type='file' name='file' class='form-control' /></div>
            <div class='row'><div class='col-md-offset-8 col-md-2'><input id='save_profile' type='submit' class='form-control btn-success' value="آپلود" /></div></div>
        </form>
    </body>
</html>
