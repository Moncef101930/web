<?php
namespace App\Form;

use App\Entity\Utilisateur;
use Gregwar\CaptchaBundle\Type\CaptchaType;
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
        $choices = [
            'User'         => 'USER',
            'Organisateur' => 'ORGANISATEUR',
        ];
        if ($options['include_admin']) {
            $choices['Admin'] = 'ADMIN';
        }

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('motDePasse', PasswordType::class, [
                'required'   => $options['password_required'],
                'mapped'     => $options['password_required'],
                'empty_data' => '',
                'attr'       => [
                    'placeholder' => $options['password_required']
                        ? ''
                        : 'Laisser vide pour conserver l’actuel',
                ],
                'label'      => 'Mot de passe',
            ])
            ->add('role', ChoiceType::class, [
                'choices' => $choices,
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'html5'  => true,
            ])
            ->add('bio')
            ->add('image', FileType::class, [
                'label'    => 'Image (File)',
                'mapped'   => false,
                'required' => false,
            ])
        ;

        if ($options['captcha_enabled']) {
            $builder->add('captcha', CaptchaType::class, [
                'label'  => 'Retapez le code ci-dessous',
                'mapped' => false,
                'attr'   => ['class' => 'form-control mt-2'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => Utilisateur::class,
            'include_admin'     => false,
            'password_required' => true,
            'captcha_enabled'   => false,
        ]);

        $resolver->setAllowedTypes('include_admin',     'bool');
        $resolver->setAllowedTypes('password_required', 'bool');
        $resolver->setAllowedTypes('captcha_enabled',   'bool');
    }
}
