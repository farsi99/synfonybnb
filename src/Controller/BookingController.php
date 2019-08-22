<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function Book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $book = new Booking();
        $form = $this->createForm(BookingType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $book->setBooker($user);
            $book->setAd($ad);
            //Si les dates ne sont pas bon ou pas disponible, erreur
            if (!$book->isBookableDates()) {
                $this->addFlash("warning", "Les dates que vous avez choisie, ne sont pas disponibles");
            } else {
                //Sinon enregistrement et redirection
                $manager->persist($book);
                $manager->flush();
                return $this->redirectToRoute('booking_show', [
                    'id' => $book->getId(),
                    'widthAlert' => true
                ]);
            }
        }
        return $this->render('booking/book.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Permet d'afficher la page de reservation
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                ->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Votre commentaire est bien envoyé');
        }
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
