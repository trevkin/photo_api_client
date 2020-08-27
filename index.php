<?php
//this makes the composer dependencies work
require "vendor/autoload.php";

//this loads constants like api credentials 
include "config.php";
include "objects/photo.php";


//this calls our composer dependencies for the guzzle session
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

include "functions.php";

$response = NULL;
$client = new Client(['verify' => false]);

//check for an id parameter somewhere in the request, if it exists we are updating rather than inserting
$id = (isset($_REQUEST["id"])?$_REQUEST["id"]:"");

//initialize some variables we will use later
$errorText = "";
$title = "";
$description = "";
$taken_at = "";
$image_ext = "";
$image_path = "";
$errorText = "Sorry, you don't appear to have selected a file.";
$uploadOk = 0;

//login to the API and get api key for our subsequent api calls
$api_token = login($api_url, $api_username, $api_password);

//the api_token will be prefixed with ERROR if login failed so check for that before proceding
if (strpos($api_token, 'ERROR') !== true)
	{
	//if we have an id then get the photos details from the API and populate the variables	
	$photo = ($id != ""?getPhoto($api_url, $api_token, $id):"");
	if ($photo instanceof Photo)
		{
		$title = $photo->title;
		$description = $photo->description;
		$taken_at = $photo->taken_at;
		$image_ext = $photo->image_ext;
		}
			
	//this checks that the form has been submitted
	if(isset($_POST["submit"]))
		{
		if(!empty($_FILES['fileToUpload']['tmp_name']) && file_exists($_FILES['fileToUpload']['tmp_name']))
			{	
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			
			// Check if image file is a actual image or fake image	
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) 
				{
				$errorText = "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
				}
			else 
				{
				$errorText = "File is not an image.";
				$uploadOk = 0;
				}
			
			// Check if file already exists
			if (file_exists($target_file)) 
				{
				$errorText = "Sorry, file already exists.";
				$uploadOk = 0;
				}
			// Check file size
			if ($_FILES["fileToUpload"]["size"] > 10000000)
				{
				$errorText = "Sorry, your file is too large.";
				$uploadOk = 0;
				}
				
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) 
				{
				$errorText = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				$uploadOk = 0;
				}
			}
		
			
		if ($uploadOk)
			{
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
				{
				$errorText = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				}
			else 
				{
				$errorText = "Sorry, there was an error uploading your file.";
				}
			$image_path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($_FILES["fileToUpload"]["name"]);
			}
			
			
			
		//this posts or puts the form elements along with a temporary url for the uploaded image to the laravel API endpoint.
		//first we check if an ID was posted, if it was then this is update rather than an insert
		if ($id != "")
			{					
			$result = updatePhoto($api_url, $api_token, $id, $title, $description, $taken_at, $image_path, $image_ext );
			}
		else
			{
			//insert must have an image, so only proceed if uploadOK is set to 1
			if ($uploadOk)
				{
				$title = $_POST["title"];
				$description = $_POST["description"];
				$taken_at = $_POST["taken_at"];
				$image_ext = $imageFileType;
				
				$result = insertPhoto($api_url, $api_token, $title, $description, $taken_at, $image_path, $image_ext );
				}
			else
				{			
				$result = $errorText;
				}
			}
			
		//it then deletes the file and displays the appropriate message
		if (strpos($result, 'SUCCESS') !== false)
			{				
			echo "<div class='alert alert-success'>Image uploaded</div>";							
			}
		else
			{				
			echo "<div class='alert alert-danger'>{$result}</div>";
			}					
		}	
	}
else
	{	
	echo "<div class='alert alert-danger'>{$api_token}</div>";				
	}
if (isset($target_file))
	{ 
	unlink($target_file);
	}		
	
//we set the page title and include the header file	 with the css links
$page_title = ($id == ""?"Upload Photos":"Edit Photos");
include "layout_header.php";

?><form name="form1" id="form1" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<?php
if ($id != "")
	{
	echo "<img src=\"".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'?"https":"http")."://{$api_url}/storage/{$id}.{$image_ext}?time={time()}\" width=\"200\" />";	
	}
?>
<table class='table table-hover table-responsive table-bordered'>
	<tr>
		<td>Upload Image</td>
		<td colspan="3"><input type="file" id="fileToUpload" name="fileToUpload" onChange="javascript:getFileDate();" <?=($id != ""?"":"required=\"true\"")?> ></td>
	</tr>
    <tr>
		<td >Title</td>
		<td ><input type="text" id="title" name="title" value="<?=$title?>" required="true"/> </td>
        <td >Date</td>
		<td ><input type="text" id="taken_at_formatted" name="taken_at_formatted" value="<?=date('d/m/Y H:i',strtotime($taken_at))?>" required="true"  autocomplete="off"/> </td>
	</tr>
	<tr>
		<td>Desciption</td>
		<td colspan="3"><textarea  id="description" name="description" style="width:100%"  required="true"/><?=$description?></textarea></td>
	</tr>
	
	<tr>
		<td></td>
		<td colspan="3"><input type="submit" value="Upload Image" name="submit"></td>
	</tr>
</table>
<input type="hidden" id="taken_at" name="taken_at" value="<?=$taken_at?>" />
<?php
if ($id != "")
	{
	echo "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"{$id}\" />";
	}
?>
</form>
<?php


//and the foooter file with our js scripts
include "layout_footer.php";
?>
