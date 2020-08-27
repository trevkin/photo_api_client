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
$page_title = "View Timeline";
include "layout_header.php";


?>
<div class="timeline-div">
	<header>
		<div class="container text-center">
			<h1>Image Timeline</h1>
		</div>
	</header>
	<section class="timeline">
		<div class="container">
			<?php

			$client = new Client(['verify' => false]);

			$response = $client->post($api_url . '/api/login',
				[
					'form_params' =>
						[
							'email' => $api_username,
							'password' => $api_password
						]
				]);
			if ($response->getStatusCode() == 200) {
				//convert the json to an object
				$someObject = json_decode($response->getBody());
				if (isset($someObject->data->api_token)) {
					$api_token = $someObject->data->api_token;

					$response = $client->get($api_url . '/api/photos',
						[
							'headers' =>
								[
									'Content-Type' => 'application/json',
									'Accept' => 'application/json',
									'Authorization' => 'Bearer ' . $api_token
								]
						]);
					if ($response->getStatusCode() == 200) {
						//convert the json to an object
						$listingArray = json_decode($response->getBody(), true);

						//var_dump($listingArray);// $response->getBody();
						//echo $listingArray[0]["id"];
						$leftOrRight = "fadeInLeft";
						for ($i = 0; $i < count($listingArray); $i++) {
							//bitwise test for an odd number
							if (($i & 1) == 0) {
								$leftOrRight = "fadeInLeft";
							} else {
								$leftOrRight = "fadeInRight";
							}
							?>
							<div class="timeline-item">
							<div class="timeline-img"></div>
							<div class="timeline-content js--<?= $leftOrRight ?>">

								<img src="http://<?= $api_url ?>/storage/<?= $listingArray[$i]["id"] ?>.<?= $listingArray[$i]["image_ext"] ?>"
									 alt="Smiley face" width="100%">
								<div class="date"><?= date('d F Y h:mA', strtotime($listingArray[$i]["taken_at"])) ?></div>
								<h2><?= $listingArray[$i]["title"] ?></h2>
								<div class="timeline-img-header">
									<p><?= $listingArray[$i]["description"] ?></p>
									<a class="bnt-more" href="javascript:void(0)">More</a>
								</div>

							</div>
							</div>
							<?php
						}
					}
				}
			}
			?>

		</div>
	</section>
</div>
<?php
//and the foooter file with our js scripts
include "layout_footer.php";
?>
