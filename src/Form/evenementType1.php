<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\evenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class evenementType1 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,        // La classe Categorie
                'choice_label' => 'nom',            // Affiche le nom des catégories
                'multiple' => true ,               // Empêche la sélection de plusieurs catégories
                'expanded' => true,                // Affiche sous forme de liste déroulante
                'by_reference' =>true,            // Nécessaire pour gérer les relations ManyToMany
                'placeholder' => 'Sélectionner une catégorie', // Option pour afficher un texte par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => evenement::class,
        ]);
    }
}
