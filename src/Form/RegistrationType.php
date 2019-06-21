<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Prénom', 'Votre prenom'))
            ->add('lastName', TextType::class, $this->getConfiguration('Nom', 'Votre nom'))
            ->add('email', EmailType::class, $this->getConfiguration('Email', 'Votre email'))
            ->add('picture', UrlType::class, $this->getConfiguration('Photo profil', 'Votre url de la photo'))
            ->add('hash', PasswordType::class, $this->getConfiguration('Mot de passe', 'Votre mot de pase'))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confirmation de mot de passe', 'Veuillez confirmer votre mot de passe'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Votre introduction'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description', 'Votre description'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
