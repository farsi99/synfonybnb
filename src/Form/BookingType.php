<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class BookingType extends ApplicationType
{
    private $transformer;
    public function __construct(FrenchToDateTimeTransformer $tranfromer)
    {
        $this->transformer = $tranfromer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', TextType::class, $this->getConfiguration("Date d'arrivée", "Date d'entrée"))
            ->add('endDate', TextType::class, $this->getConfiguration("Date Fin", "Date de départ"))
            ->add('comment', TextareaType::class, $this->getConfiguration(false, "Description de votre message", ["required" => false]));

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => [
                'Default', 'front'
            ]
        ]);
    }
}
