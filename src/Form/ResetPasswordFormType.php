<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('plainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'first_options'   => [
                'label'=>'Nouveau mot de passe',
                'attr'=>['class'=>'form-control']
            ],
            'second_options'  => [
                'label'=>'Confirmer',
                'attr'=>['class'=>'form-control']
            ],
            'invalid_message' => 'Les mots de passe doivent correspondre.',
        ]);
    }
}