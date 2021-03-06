<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class Statistique
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getStats()
    {
        $users = $this->getUsersCount();
        $bookings = $this->getBookingsCount();
        $comments = $this->getCommentsCount();
        $ads = $this->getAdsCount();
        return compact('users', 'bookings', 'comments', 'ads');
    }


    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsCount()
    {
        return  $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsCount()
    {

        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getBestAds()
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note,a.title,u.firstName,u.lastName,u.picture 
             FROM App\Entity\Comment c
             JOIN c.ad a
             JOIN a.author u
             GROUP BY a
             ORDER BY note DESC'
        )->setMaxResults(5)->getResult();
    }

    public function getwortsAds()
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note,a.title,u.firstName,u.lastName,u.picture 
             FROM App\Entity\Comment c
             JOIN c.ad a
             JOIN a.author u
             GROUP BY a
             ORDER BY note ASC'
        )->setMaxResults(5)->getResult();
    }
}
