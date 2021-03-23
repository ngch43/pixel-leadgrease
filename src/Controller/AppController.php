<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Campaign;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('app_list_campaigns');
    }

    /**
     * @Route("/campaigns", name="app_list_campaigns")
     */
    public function listCampaigns(): Response
    {

        $campaigns = $this->getDoctrine()
            ->getRepository(Campaign::class)
            ->findAll();

        return $this->render('app/campaigns/index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }
    /**
     * @Route("/app/campaign/view", name="app_campaign_view")
     */
    public function viewCampaign(Request $request): Response
    {
        $errors = [];

        $campaign = new Campaign();
        $campaign_id = $request->query->get('campaign_id');

        if(!empty($campaign_id)){
            $campaign = $this->getDoctrine()
                ->getRepository(Campaign::class)
                ->find($campaign_id);
            if (!$campaign) {
                throw $this->createNotFoundException(
                    'No campaign found for id '.$campaign_id
                );
            }
        }
        
        if ($request->isMethod('POST')) {

            
            $campaign->setToken($request->get('token'));
            $campaign->setPixel($request->get('pixel'));
            $campaign->setUrl($request->get('url'));
            $campaign->setMethod($request->get('method'));

            /* $errors = $validator->validate($campaign);
            if (count($errors) == 0) { */
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($campaign);
                $entityManager->flush();
                return $this->redirectToRoute('app_list_campaigns');
            /* } */

        }
        return $this->render('app/campaigns/view.html.twig',[
            'errors' => $errors,
            'campaign' => $campaign
        ]);
    }
}
