<?php



function login ($username, $password)
	{
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
				return $someObject->data->api_token;
				}
			else
				{
				return "ERROR:Token not there?";	
				}
			}
		else
			{
			return "ERROR:{$response->getBody()}";	
			}
		}
	catch (GuzzleHttp\Exception\ClientException $e) 
		{		
		return "ERROR:{$e}";
		}
	}
	
function updatePhoto ($apiToken, $id, $title, $description, $takenAt, $imagePath, $imageFileType )
	{
	try 
		{		
		$response = $client->put($api_url.'/api/photos/'.$id, 
			[
			'json' => 
				[ 
				'id' => $id,	
				'title' => $title,
				'description' => $description,
				'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($imagePath),
				'taken_at' => 	$takenAt,
				'image_ext' => 	$imageFileType
				],
			'headers' => 
				[
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer '.$apiToken
				]
			//This handler is called if we want to see what guzzler is posting	
			//	,'handler' => $tapMiddleware($clientHandler)
			]);	
		if ($response->getStatusCode() == 201)
			{
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			if (isset($someObject->data->id))
				{
				return "SUCCESS: The image was updated";
				}
			else
				{
				return "ERROR: The image was NOT updated";
				}
			}
		else
			{
			return "ERROR:{$response->getBody()}";	
			}
		}
	catch (GuzzleHttp\Exception\ClientException $e) 
		{		
		return "ERROR:{$e}";
		}
	}
	
function insertPhoto ($apiToken, $title, $description, $takenAt, $imagePath, $imageFileType )
	{
	try 
		{		
		$response = $client->post($api_url.'/api/photos/', 
			[
			'json' => 
				[ 				
				'title' => $title,
				'description' => $description,
				'url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".rawurlencode($imagePath),
				'taken_at' => 	$takenAt,
				'image_ext' => 	$imageFileType
				],
			'headers' => 
				[
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Bearer '.$apiToken
				]
			//This handler is called if we want to see what guzzler is posting	
			//	,'handler' => $tapMiddleware($clientHandler)
			]);	
		if ($response->getStatusCode() == 201)
			{
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			if (isset($someObject->data->id))
				{
				return "SUCCESS: The image was updated";
				}
			else
				{
				return "ERROR: The image was NOT updated";
				}
			}
		else
			{
			return "ERROR:{$response->getBody()}";	
			}
		}
	catch (GuzzleHttp\Exception\ClientException $e) 
		{		
		return "ERROR:{$e}";
		}
	}