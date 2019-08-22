<?php


namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if ($date === null) {
            return '';
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        if ($frenchDate === null) {
            //exception 
            throw new TransformationFailedException("Vuillez saisir une date");
        }
        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);
        if ($date === null) {
            throw new TransformationFailedException("Veuillez saisir un format de date valide");
        }

        return $date;
    }
}
