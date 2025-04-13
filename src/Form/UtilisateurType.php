<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('motDePasse',PasswordType::class,[
                'required'=>true
            ])
            ->add('role', ChoiceType::class, [
                'choices'  => [
                    'User'         => 'USER',
                    'Admin'        => 'ADMIN',
                    'Organisateur' => 'ORGANISATEUR',
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'html5'=>true,
            ])
            ->add('bio')
            ->add('image',FileType::class,[
                'label'=> 'Image (File)',
                'mapped'=>false,
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
