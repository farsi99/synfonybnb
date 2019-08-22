<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $AdRepo, UserRepository $users)
    {
        return $this->render(
            'home.html.twig',
            [
                'ads' => $AdRepo->findBestAds(3),
                'users' => $users->findBestUsers()
            ]
        );
    }
}
