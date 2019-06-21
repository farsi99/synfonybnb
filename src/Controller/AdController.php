<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


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
     * @IsGranted("ROLE_USER")
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
            //lier un utilisateur à une annonce
            $ad->setAuthor($this->getUser());
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
     * On verifique l'utilisateur est connecté et qu'il est l'auteur de l'annonce
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifié")
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
    /** 
     * Permet de supprimer une annonce
     * @Route("/ads/{slug}/delete/", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user==ad.getAuthor()",message="Vous n'avez pas le droit d'accèder à cette ressource")
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("success", "L'annonce <strong>{$ad->getTitle()}</strong> est supprimée avec succès !");
        return $this->redirectToRoute("ad_index");
    }
}
