<?php

namespace App\Form;

use App\Entity\Station;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomS')
            ->add('adresseS')
            ->add('ligneS')
            ->add('horaireS')
            ->add('equipementS')
            ->add('commentaireS')
            ->add('circuit')
            ->add('pos1')
            ->add('pos2')
            ->add('image', FileType::class, ['mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Station::class,
        ]);
    }
}
