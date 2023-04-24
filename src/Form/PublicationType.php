<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre',null,['label' => 'Titre :',    'label_attr' => [
            'style' => 'display: block;color: #3d4465; ',
        ]])

            ->add('texte', TextareaType::class, [
                'label' => 'Contenu :',    'label_attr' => [
                    'style' => 'display: block;color: #3d4465; ',
                ],
               
            
                 'attr' => [ 'rows' => 5,
                'cols' => 50,
                ],])

                ->add('image', FileType::class, [
                 /*   'label' => 'Image (jpeg, png, gif,pdf)',*/
                 'label' => 'Image/Video :',
                 'label_attr' => [
                    'style' => 'display: block;color: #3d4465; '],
                    'mapped' => false,
                    'required' => false,
                ]);
           // ->add('datePub')
           /* ->add('user', null, [
                'choice_label' => function ($user) {
                    return $user->getidUsr();
                }
            ])*/
           /* ->add('idUser', null, [
                'choice_label' => function ($user) {
                    return $user->getidUsr();
                }
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
