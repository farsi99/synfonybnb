<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Ad;
use App\Form\AnnonceType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;


class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ad_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     *
     * @return Response
     */
    public function create(ObjectManager $manager, Request $request)
    {
        $ad = new Ad();

        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre annonce <strong> </strong> est enregistrée avec succès !"
            );
            return $this->redirectToRoute('ads_create');
        }
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de d'afficher le formulaire d'edition 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return Response
     */
    public function edit(Ad $ad, Request $request,  ObjectManager $manager)
    {
        //on crée un formulaire via le form AnnonceType, et on l'associe avec les données de l'entité $ad
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', 'Modification effectuée avec succès !');
            $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug()]);
        }
        return $this->render(
            'ad/edit.html.twig',
            [
                'form' => $form->createView(),
                'ad' => $ad
            ]
        );
    }

    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', ['ad' => $ad]);
    }
}
