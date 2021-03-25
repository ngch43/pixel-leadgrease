<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Exception\CampaignNotFoundException;
use App\LeadgreaseLib\Client;
use App\Entity\Campaign;

use App\Api\ApiResponse;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(): Response
    {
        return new ApiResponse([],[],'¡PixelLeadgrease api!');
    }

    /**
     * @Route("/api/proxy/{token}", name="api_leadgrease", requirements={"token"=".+"})
     */
    public function proxy(string $token, Request $request): Response
    {

        $campaign = $this->getDoctrine()
            ->getRepository(Campaign::class)
            ->findOneBy([
                'token' => $token,
            ]);

        if (!$campaign) {
            throw new CampaignNotFoundException( 'Token not found '.$token );
        }

        $leadgrease_client = new Client();

        $pixel_response = $leadgrease_client->getPixelResponse($campaign->getPixel());
        $info = $leadgrease_client->getInfo();
        $url = $campaign->getUrl();
        $method = $campaign->getMethod();

        $info_data = $info['fields'];
        if($method == 'GET')
            $info_data = $info['query'];

        try {
            $response = $leadgrease_client->sendInfo($url,$method,$info_data,$info['headers']); 
            $data = [
                'pixel' => $pixel_response,
                'response' => $response
            ];
            return new ApiResponse($data);
        } catch (\Throwable $th) {
            $data = [
                'pixel' => 'ko_pixel'
            ];
            var_dump($th);
            return new ApiResponse($data);
        }
        
        
    }
}
