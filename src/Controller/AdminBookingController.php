<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/booking/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $book, $page, Pagination $pagination)
    {
        $pagination->setEntityClass(Booking::class)
            ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * cette méthode permet de gerer la modification d'une annonce
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     *
     * @return Reponse
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();
            $this->addFlash("success", "la reservation n°{$booking->getId()} a bien été modifié! ");
            return $this->redirectToRoute("admin_booking_index");
        }
        return $this->render("admin/booking/edit.html.twig", [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * cette méthode supprime une reservation
     * @Route("/admin/booking/{id}/delete", name="admin_booking_delete")
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager)
    {
        if ($booking) {
            $manager->remove($booking);
            $manager->flush();
            $this->addFlash("success", "la suppression est bien effectuée !");
            return $this->redirectToRoute("admin_booking_index");
        } else {
            return $this->redirectToRoute("admin_booking_index");
        }
    }
}
