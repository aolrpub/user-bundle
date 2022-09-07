<?php

namespace Aolr\UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserResetPasswordType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => $this->translator->trans('form.UserResetPasswordType.password.invalid_message', [], 'aolr_user'),
                'required' => true,
                'first_options' => [
                    'label' => 'form.UserResetPasswordType.password.label_1',
                    'attr' => ['placeholder' => 'form.UserResetPasswordType.password.label_1']
                ],
                'second_options' => [
                    'label' => 'form.UserResetPasswordType.password.label_2',
                    'attr' => ['placeholder' => 'form.UserResetPasswordType.password.label_2']
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6, 'max' => 4096])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'translation_domain' => 'aolr_user'
        ]);
    }
}
