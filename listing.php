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
include "functions.php";

?><div class="container">
    <table class='table table-hover table-responsive table-bordered'>
   <tr>
   		<td>Image</td>
		<td>Title</td>
		<td>Description</td>
        <td>Date</td>
        <td></td>
        <td></td>
	</tr> <?php
	
//login to the API and get api key for our subsequent api calls
$api_token = login($api_url, $api_username, $api_password);

//the api_token will be prefixed with ERROR if login failed so check for that before proceding
if (strpos($api_token, 'ERROR') !== true)
	{
	$client = new Client(['verify' => false]);
	
	//if delete_id is in request, call delete function
	if (isset($_REQUEST["delete_id"]))
		{
		$response = $client->delete($api_url.'/api/photos/'.$_REQUEST["delete_id"], 
			[			
			'headers' => 
				[
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer '.$api_token
				]
			]);
		if ($response->getStatusCode() == 204)
			{
			echo "<div class='alert alert-success'>Photo deleted</div>";
			}			
		}
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
   		<td><img src="http://<?=$api_url?>/storage/<?=$listingArray[$i]["id"]?>.<?=$listingArray[$i]["image_ext"]?>?time=<?=time()?>" alt="<?=$listingArray[$i]["title"]?>"  width="200"></td>
		<td><?=$listingArray[$i]["title"]?></td>
		<td><?=$listingArray[$i]["description"]?></td>
        <td><?=date('d F Y h:mA', strtotime($listingArray[$i]["taken_at"]))?></td>
        <td><a href="index.php?id=<?=$listingArray[$i]["id"]?>">Edit</a></td>
        <td><a href="#" data-href="listing.php?delete_id=<?=$listingArray[$i]["id"]?>" data-toggle="modal" data-target="#confirm-delete">Delete</a></td>
	</tr>
<?php	
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
