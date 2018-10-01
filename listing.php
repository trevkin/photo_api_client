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

//we set the page title and include the header file	 with the css links
$page_title = "View Listing";
include "layout_header.php";


?>

	<div class="container">
    <table class='table table-hover table-responsive table-bordered'>
   <tr>
   		<td>Image</td>
		<td>Title</td>
		<td>Description</td>
        <td>Date</td>
        <td></td>
        <td></td>
	</tr> <?php
	
	
	
	$client = new Client(['verify' => false]);
			
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
		
		$response = $client->get($api_url.'/api/photos', 
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
			$listingArray = json_decode($response->getBody(), true);
			
			//var_dump($listingArray);// $response->getBody();
			//echo $listingArray[0]["id"];
			$leftOrRight = "fadeInLeft";
			for($i=0;$i< count($listingArray);$i++) 
				{
				//bitwise test for an odd number
				if (($i & 1)==0)
					{
					$leftOrRight  = "fadeInLeft";	
					}
				else
					{
					$leftOrRight  = "fadeInRight";	
					}
?> <tr>
   		<td><img src="http://<?=$api_url?>/storage/<?=$listingArray[$i]["id"]?>.<?=$listingArray[$i]["image_ext"]?>" alt="<?=$listingArray[$i]["title"]?>"  width="200"></td>
		<td><?=$listingArray[$i]["title"]?></td>
		<td><?=$listingArray[$i]["description"]?></td>
        <td><?=date('d F Y h:mA', strtotime($listingArray[$i]["taken_at"]))?></td>
        <td><a href="http://<?=$api_url?>/api/photos/<?=$listingArray[$i]["id"]?>">Edit</a></td>
        <td><a href="">Delete</a></td>
	</tr>
<?php	
				}
			}
		}
	}
?>
	</table>
    </div>

<?php
//and the foooter file with our js scripts
include "layout_footer.php";
?>
