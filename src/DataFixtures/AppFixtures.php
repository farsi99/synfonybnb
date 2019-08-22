<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        //creéons un role pour les users
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        //On crée un admin par défaut
        $adminUser = new User();
        $adminUser->setFirstName('Farouk');
        $adminUser->setLastName('Soulé');
        $adminUser->setEmail('farsi_99@hotmail.com');
        $adminUser->setHash($this->encoder->encodePassword($adminUser, 'password'));
        $adminUser->setPicture('https://avatars.io/twitter/LiiorC');
        $adminUser->setIntroduction($faker->sentence());
        $adminUser->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>');
        $adminUser->addUserRole($adminRole);
        $manager->persist($adminUser);
        //Nous gerons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture = $picture . ($genre == "male" ? "men/" : "women/") . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setPicture($picture)
                ->setHash('password')
                ->setHash($hash);

            $manager->persist($user);
            $users[] = $user;
        }
        //Nous gerons les annonces
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            //on rajoute les utilisateurs d'une maniere aléatoirs sur les anonces
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setIntroduction($introduction)
                ->setCoverImage($coverImage)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRomms(mt_rand(1, 6))
                ->setAuthor($user);


            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($title)
                    ->setAd($ad);

                $manager->persist($image);
            }
            //Gestion des reservations
            for ($j = 0; $j <= mt_rand(0, 10); $j++) {
                $book = new Booking();
                $cretedAt = $faker->dateTime('-6 months');
                $stardDate = $faker->dateTimeBetween('- months');
                //gestion de la date de fin
                $duration = mt_rand(3, 10);
                $endDate = (clone $stardDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];
                $coment = $faker->paragraph();
                //configuration
                $book->setBooker($booker)
                    ->setAmount($amount)
                    ->setCreatedAt($cretedAt)
                    ->setStartDate($stardDate)
                    ->setEndDate($endDate)
                    ->setAd($ad)
                    ->setComment($coment);

                $manager->persist($book);
                //Gestion des commentaires
                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                        ->setRating(mt_rand(1, 5))
                        ->setAuthor($booker)
                        ->setAd($ad);

                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
