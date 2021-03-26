<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Ramsey\Uuid\Uuid;

use App\Entity\Campaign;
use App\Entity\User;


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
     * @Route("/app/campaigns", name="app_list_campaigns")
     */
    public function listCampaigns(): Response
    {
        $user = $this->getUser();

        $campaigns = $this->getDoctrine()
            ->getRepository(Campaign::class)
            ->findBy([
                'user' => $user
            ]);

        return $this->render('app/campaigns/index.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }
    /**
     * @Route("/app/campaign/view", name="app_campaign_view")
     */
    public function viewCampaign(Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];

        $campaign = new Campaign();
        $campaign_id = $request->query->get('campaign_id');

        // $user = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->getUser();
        if(!empty($campaign_id)){
            $campaign = $this->getDoctrine()
                ->getRepository(Campaign::class)
                ->find($campaign_id);
            if (!$campaign) {
                throw $this->createNotFoundException(
                    'No campaign found for id '.$campaign_id
                );
            }
        }else{
            $campaign->setToken(Uuid::uuid4());
        }

        
        if ($request->isMethod('POST')) {
            
            
            $campaign->setToken($request->get('token'));
            $campaign->setName($request->get('name'));
            $campaign->setPixel($request->get('pixel'));
            $campaign->setUrl($request->get('url'));
            $campaign->setMethod($request->get('method'));

            $active = $request->get('active')? true:false;
            $campaign->setActive($active);
            $campaign->setUser($user);
            $errors = $validator->validate($campaign);
            if (count($errors) == 0) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($campaign);
                $entityManager->flush();
                return $this->redirectToRoute('app_list_campaigns');
            }

        }
        return $this->render('app/campaigns/view.html.twig',[
            'errors' => $errors,
            'campaign' => $campaign
        ]);
    }

    /**
     * @Route("/app/campaign/delete", name="app_campaign_delete")
     */
    public function deleteCampaign(Request $request): Response
    {
        $campaign_id = $request->query->get('campaign_id');

        if(!empty($campaign_id)){
            $campaign = $this->getDoctrine()
                ->getRepository(Campaign::class)
                ->find($campaign_id);
            if ($campaign) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($campaign);
                $entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_list_campaigns');
    }
}
