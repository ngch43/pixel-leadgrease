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

        /* if($campaign->getMethod())
            $info['method'] = $campaign->getMethod(); */

        try {
            $response = $leadgrease_client->sendInfo($url,$info); 
            $data = [
                'pixel' => $pixel_response,
                'response' => $response['data']
            ];
            
            return new ApiResponse($data,$response['code']);
        } catch (\Throwable $th) {
            var_dump($th);
            $data = [
                'pixel' => 'ko_pixel'
            ];
            return new ApiResponse($data,500);
        }
        
        
    }
}
