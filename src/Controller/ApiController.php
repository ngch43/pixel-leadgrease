<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        $leadgrease_client->setPixel($client->getPixel());
        $data = $leadgrease_client->getInfo();
        $data['url'] = $campaign->getUrl();
        $data['method'] = $campaign->getMethod();

        $response = $leadgrease_client->sendInfo($data);   
        unset($data['url']);
        unset($data['method']);

        $data = [
            'request' => $data,
            'response' => $response
        ];
        return new ApiResponse($data);
        
    }
}
