<?php

namespace Aolr\UserBundle\Form;

use Aolr\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegisterType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'label' => 'form.UserRegisterType.email.label',
                'required' => true,
                'attr' => ['placeholder' => 'form.UserRegisterType.email.placeholder'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);
                        if (!empty($user)) {
                            $context->addViolation($this->translator->trans('form.UserRegisterType.email.constraints.callback_1', [], 'aolr_user'));
                        }
                    })
                ]
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'invalid_message' => $this->translator->trans('form.UserRegisterType.password.invalid_message', [], 'aolr_user'),
                'required' => true,
                'first_options' => [
                    'label' => 'form.UserRegisterType.password.label_1',
                    'attr' => ['placeholder' => 'form.UserRegisterType.password.label_1']
                ],
                'second_options' => [
                    'label' => 'form.UserRegisterType.password.label_2',
                    'attr' => ['placeholder' => 'form.UserRegisterType.password.label_2']
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 6, 'max' => 4096])
                ],
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => '',
                'required' => true,
                'attr' => ['placeholder' => 'form.UserRegisterType.captcha.placeholder'],
                'width' => 200,
                'length' => 6,
                'as_url' => true,
                'reload' => true
            ])
//            ->add('term', Type\CheckboxType::class, [
//                'label' => 'terms & conditions',
//                'required' => true
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'translation_domain' => 'aolr_user'
        ]);
    }
}
