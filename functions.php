<?php

//this calls our composer dependencies for the guzzle session
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

function login ($api_url, $api_username, $api_password)
	{
	try 
		{
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
	
function updatePhoto ($api_url, $api_token, $id, $title, $description, $taken_at, $image_path, $image_ext )
	{
	try 
		{
		// Create a middleware that echoes parts of the request if we need to test it.
		$tapMiddleware = Middleware::tap(function ($request) 
			{
			//dump out the headers
			var_dump($request->getHeaders());
			//and the body
			echo $request->getBody();						
			});	
						
		$client = new Client(['verify' => false]);
		// Grab the client's handler instance.
		$clientHandler = $client->getConfig('handler');
			
		$response = $client->put($api_url.'/api/photos/'.$id, 
			[
			'json' => 
				[
				'title' => $title,
				'description' => $description,
				'url' => $image_path,
				'taken_at' => 	$taken_at,
				'image_ext' => 	$image_ext
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
		if ($response->getStatusCode() == 200)
			{
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			if (isset($someObject->id))
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
	
function insertPhoto ($api_url, $api_token, $title, $description, $taken_at, $image_path, $image_ext )
	{
	try 
		{
		// Create a middleware that echoes parts of the request if we need to test it.
		$tapMiddleware = Middleware::tap(function ($request) 
			{
			//dump out the headers
			var_dump($request->getHeaders());
			//and the body
			echo $request->getBody();						
			});		
			
		$client = new Client(['verify' => false]);			
		
		// Grab the client's handler instance.
		$clientHandler = $client->getConfig('handler');
		
		$response = $client->post($api_url.'/api/photos', 
			[
			'json' => 
				[ 				
				'title' => $title,
				'description' => $description,
				'url' => $image_path,
				'taken_at' => 	$taken_at,
				'image_ext' => 	$image_ext
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
		if ($response->getStatusCode() == 201)
			{
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			if (isset($someObject->id))
				{				
				return "SUCCESS: The image was updated";
				}
			else
				{
				echo $response->getBody();
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
	
function getPhoto ($api_url, $api_token, $id)
	{	
	try 
		{	
		$client = new Client(['verify' => false]);	
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
			
			$photo = new Photo();
			//convert the json to an object
			$someObject = json_decode($response->getBody());
			$photo->id = $someObject->id;
			$photo->title = $someObject->title;
			$photo->description = $someObject->description;
			$photo->taken_at = $someObject->taken_at;
			$photo->image_ext = $someObject->image_ext;
			
			return $photo;
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
	