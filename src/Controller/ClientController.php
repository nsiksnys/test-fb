<?php

namespace App\Controller;

use Facebook\Facebook;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientController extends Controller
{
	protected $containerInterface;
	protected $logger;
	protected $facebookClient;

	public function __construct(ContainerInterface $containerInterface)
    {
        $this->containerInterface = $containerInterface;
        $this->logger = $containerInterface->get("monolog.logger.fb-test");
    }

	/**
     * @Route("/profile/facebook/{id}", name="profile")
     */
	public function getProfileAction($profileId)
	{
		$this->facebookClient = $this->getConnection();
		$accessToken = $this->getAccessToken();

		try 
		{
			$this->facebookClient->get("/$profileId", $accessToken);
			$user = $response->getGraphUser();
		}
		catch(\Facebook\Exceptions\FacebookExceptionsFacebookResponseException $e) 
		{
		  $this->logger->err('Graph error: ' . $e->getMessage());
		  throw new HttpException(500, "Something went wrong. Please try again later.");
		}
		catch(\Facebook\Exceptions\FacebookExceptionsFacebookSDKException $e) 
		{
		  $this->logger->err('Facebook SDK error: ' . $e->getMessage());
		  throw new HttpException(500, "Something went wrong. Please try again later.");
		}
		catch(\Exception $e)
		{
			$this->logger->err('Error: ' . $e->getMessage());
		  throw new HttpException(500, "Something went wrong. Please try again later.");
		}
		return json_encode($user);
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
			throw new AccessDeniedException("Something went wrong. Please check your credentials.");
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
			throw new AccessDeniedException("Something went wrong. Please check your credentials.");
		}
	}

	public function getConnectionParams()
	{
		return [
			'app_id' => $this->containerInterface->getParameter("APP_ID");
			'app_secret' => $this->containerInterface->getParameter("APP_SECRET");
			'default_graph_version' => $this->containerInterface->getParameter("API_VERSION");
		];
	}
}