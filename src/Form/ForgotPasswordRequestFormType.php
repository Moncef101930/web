<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ForgotPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('email', EmailType::class, [
            'label' => 'Votre adresse e-mail',
            'attr'  => ['class'=>'form-control'],
        ]);
    }
}
