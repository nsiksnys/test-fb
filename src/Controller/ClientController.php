<?php

namespace App\Controller;

use Facebook\Facebook;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientController extends FOSRestController
{
	protected $containerInterface;
	protected $logger;
	protected $facebookClient;

	public function __construct(ContainerInterface $containerInterface)
    {
        $this->containerInterface = $containerInterface;
        $this->logger = $containerInterface->get("monolog.logger.fbclient");
    }

	/**
     * @Get("/profile/facebook/{profileId}", name="profile")
     */
	public function getProfileAction($profileId)
	{
		$this->facebookClient = $this->getConnection();

		try 
		{
			$this->logger->info("Requested profile $profileId");
			$facebookResponse = $this->facebookClient->get("/$profileId");
			$user = $facebookResponse->getGraphUser()->uncastItems();
			return $this->json($user);
		}
		catch(\Facebook\Exceptions\FacebookResponseException $e) 
		{
		  $this->logger->err('Graph: ' . $e->getMessage());
		  return new JsonResponse(["message" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		catch(\Facebook\Exceptions\FacebookSDKException $e) 
		{
		  $this->logger->err('Facebook SDK: ' . $e->getMessage());
		  return new JsonResponse(["message" => "Something went wrong. Please try again later."], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		catch(\Exception $e)
		{
			$this->logger->err($e->getMessage());
		  return new JsonResponse(["message" => "Something went wrong. Please try again later."], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	public function getAccessToken()
	{
		try
		{
			return $this->facebookClient->getCanvasHelper()->getAccessToken();
		}
		catch(\Facebook\Exceptions\FacebookResponseException $e)
		{
			$this->logger->err("Could not get access token. ".$e->getMessage());
			return new JsonResponse(["message" => "Something went wrong. Please check your credentials."], Response::HTTP_FORBIDDEN);
		}
	}

	public function getConnection()
	{
		try
		{
			return new Facebook($this->getConnectionParams());
		}
		catch(\Facebook\Exceptions\FacebookSDKException $e)
		{
			$this->logger->err("Could not open connection. ".$e->getMessage());
			return new JsonResponse("Something went wrong. Please check your credentials.", Response::HTTP_FORBIDDEN);
		}
	}

	public function getConnectionParams()
	{
		return [
			'app_id' => $this->containerInterface->getParameter("app_id"),
			'app_secret' => $this->containerInterface->getParameter("app_secret"),
			'default_graph_version' => 'v'.$this->containerInterface->getParameter("api_version"),
			'default_access_token' => $this->containerInterface->getParameter("app_token")
		];
	}
}