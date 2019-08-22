<?php

namespace App\Controller;

use App\Service\Statistique;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, Statistique $stat)
    {
        $stats = $stat->getStats();
        //On veut afficher les annonces les mieux notées
        $bestAds = $stat->getBestAds();
        //On veut les annonces males notés
        $worstAds = $stat->getwortsAds();


        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds

        ]);
    }
}
