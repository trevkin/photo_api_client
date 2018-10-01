<?php
//this makes the composer dependencies work
require "vendor/autoload.php";

//this loads constants like api credentials 
include "config.php";

//this calls our composer dependencies for the guzzle session
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

$response = NULL;
$client = new Client(['verify' => false]);
$id = (isset($_REQUEST["id"])?$_REQUEST["id"]:"");
$errorText = "";

$title = "";
$description = "";
$taken_at = "";
//login and get api key
try 
	{		
	$response = $client->post($api_url.'/api/login', 
		[
		'form_params' => 
			[
			'email' => $api_username,
			'password' => $api_password
			]
		]);
	if ($response->getStatusCode() == 200)
		{
		//convert the json to an object
		$someObject = json_decode($response->getBody());
		if (isset($someObject->data->api_token))
			{
			$api_token = $someObject->data->api_token;
			}
		}
	
	if ($api_token != "")
		{	
		$response = $client->get($api_url.'/api/photos/'.$id, 
			[						
			'headers' => 
				[
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer '.$api_token
				]
			]);
		if ($response->getStatusCode() == 200)
			{
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			
			$title = $someObject->data->title;
			$description = $someObject->data->description;
			$taken_at = $someObject->data->taken_at;
			}
			
		//this checks that the form has been posted
		if(isset($_POST["submit"]))
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
					
				// Grab the client's handler instance.
				$clientHandler = $client->getConfig('handler');
				
				// Create a middleware that echoes parts of the request if we need to test it.
				$tapMiddleware = Middleware::tap(function ($request) 
					{
					//dump out the headers
					var_dump($request->getHeaders());
					//and the body
					echo $request->getBody();						
					});
					
				echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($_FILES["fileToUpload"]["name"]);
				//this posts or puts the form elements along with a temporary url for the uploaded image to the laravel API endpoint.
				//first we check if an ID was posted, if it was then this is update rather than an insert
				if ($id != "")
					{
					$response = $client->put($api_url.'/api/photos/'.$id, 
						[
						'json' => 
							[ 
							'id' => $id,	
							'title' => $_POST['title'],
							'description' => $_POST['description'],
							'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($_FILES["fileToUpload"]["name"]),
							'taken_at' => 	$_POST['taken_at'],
							'image_ext' => 	$imageFileType
							],
						'headers' => 
							[
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
							'Authorization' => 'Bearer '.$api_token
							]
						//This handler is called if we want to see what guzzler is posting	
						//	,'handler' => $tapMiddleware($clientHandler)
						]);								
					}
				else
					{
					$response = $client->post($api_url.'/api/photos', 
						[
						'json' => 
							[ 	
							'title' => $_POST['title'],
							'description' => $_POST['description'],
							'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($_FILES["fileToUpload"]["name"]),
							'taken_at' => 	$_POST['taken_at'],
							'image_ext' => 	$imageFileType
							],
						'headers' => 
							[
							'Content-Type' => 'application/json',
							'Accept' => 'application/json',
							'Authorization' => 'Bearer '.$api_token
							]
						//This handler is called if we want to see what guzzler is posting	
						//	,'handler' => $tapMiddleware($clientHandler)
						]);
					}
					
				//it then deletes the file and displays the appropriate message
				if ($response->getStatusCode() == 201)
					{
					unlink($target_file);
					echo "<div class='alert alert-success'>Image uploaded</div>";							
					}
				else
					{
					unlink($target_file);
					echo "<div class='alert alert-danger'>Something went wrong. Error Code:{$response->getStatusCode()}</div>";
					}		
				}
			else
				{
				unlink($target_file);
				echo "<div class='alert alert-danger'>{$errorText}</div>";
				}
			}
		}
	}
catch (GuzzleHttp\Exception\ClientException $e) 
	{
	if (isset($target_file))
		{ 
		unlink($target_file);
		}
	echo "<div class='alert alert-danger'>{$e}</div>";
	}
	
//we set the page title and include the header file	 with the css links
$page_title = "Upload Images";
include "layout_header.php";

?><form name="form1" id="form1" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<table class='table table-hover table-responsive table-bordered'>
	<tr>
		<td>Upload Image</td>
		<td colspan="3"><input type="file" id="fileToUpload" name="fileToUpload" onChange="javascript:getFileDate();" required="true"></td>
	</tr>
    <tr>
		<td >Title</td>
		<td ><input type="text" id="title" name="title" value="<?=$title?>" required="true"/> </td>
        <td >Date</td>
		<td ><input type="text" id="taken_at_formatted" name="taken_at_formatted" value="<?=$taken_at?>" required="true"  autocomplete="off"/> </td>
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
	echo "<input type=\"hidden\" id=\"id\" name=\"id\" value=\"{id}\" />";
	}
?>
</form>
<?php


//and the foooter file with our js scripts
include "layout_footer.php";
?>
