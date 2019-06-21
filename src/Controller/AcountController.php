<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AcountController extends AbstractController
{
    /**
     * @Route("/login", name="acount_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('acount/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * cette méthode permet de se deconnecter
     * @Route("/logout" , name="acount_logout")
     *
     * @return void
     */
    public function logout()
    { }

    /**
     * cette méthode traite l'inscription des utilisateurs
     * @Route("/register", name="acount_register")
     * @return Response 
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Votre compte est crée avec succès, vous pouvez maintenat vous connectez!');
            $this->redirectToRoute('acount_login');
        }
        return $this->render('acount/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de modifier un profil utilisateur
     * @Route("/acount/profil", name="acount_profil")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profil(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'les données du profil ont été enregistrer avec succès');
        }
        return $this->render('acount/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *@Route("/acount/update-password", name="acount_password")
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //verification si l'ancienne mot de passe est la bonne
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //gerer l'erreur 
                $form->get('oldPassword')->addError(new FormError("Le mot de passe n'est pas le bon mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Modification effectuée avec succès');
                return $this->redirectToRoute('acount_index');
            }
        }
        return $this->render('acount/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher mon profil
     * @Route("/acount", name="acount_index")
     * @IsGranted("ROLE_USER")
     * @return Response 
     * */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
