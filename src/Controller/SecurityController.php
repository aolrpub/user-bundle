<?php

namespace Aolr\UserBundle\Controller;

use Aolr\NotificationBundle\Entity\EmailTemplateGroup;
use Aolr\NotificationBundle\Service\MailSender;
use Aolr\NotificationBundle\Service\TemplateManager;
use Aolr\UserBundle\Entity\ResetPassword;
use Aolr\UserBundle\Entity\UserValidation;
use Aolr\UserBundle\Form\UserRegisterType;
use Aolr\UserBundle\Entity\User;
use Aolr\UserBundle\Form\UserResetPasswordType;
use Aolr\UserBundle\Service\ConfigManager;
use Aolr\UserBundle\Service\Helper;
use Aolr\UserBundle\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/user")
 */
class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(EntityManagerInterface $em, ConfigManager $config, UserManager $userManager, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->config = $config;
        $this->userManager = $userManager;
        $this->translator = $translator;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param SessionInterface $session
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
        if ($this->getUser() && $this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute($this->config->getAfterLoginRoute());
        }

        if ($request->get('target_path', false)) {
            $session->set('_security.main.target_path', $request->get('target_path'));
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@AolrUser/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordEncoder
     *
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder)
    {
        $form = $this->createForm(UserRegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = new User();
            $user
                ->setEmail($data['email'])
                ->setPassword($passwordEncoder->hashPassword($user, $data['password']))
                ->setRegisterAt(new \DateTime())
            ;

            $this->em->persist($user);
            $this->em->flush();

            $this->userManager->sendVerificationEmail($user);

            return $this->redirectToRoute('user_confirmation', [
                'type' => 'registration',
                'email' => $user->getEmail()
            ]);
        }

        return $this->render('@AolrUser/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/send-verification-code", name="_ajax_user_verification_code")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function sendEmailCode(Request $request): JsonResponse
    {
        $this->userManager->sendCode($request->get('email', ''));
        return $this->json([]);
    }

    /**
     * @Route("/forgot-password", name="user_forgot_password")
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function forgotPassword(Request $request): Response
    {
        $form = $this
            ->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => $this->translator->trans('form.ForgotPassword.email.label', [], 'aolr_user'),
                'required' => true,
                'attr' => ['placeholder' => $this->translator->trans('form.ForgotPassword.email.label', [], 'aolr_user')],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $value]);
                        if (empty($user)) {
                            $context->addViolation($this->translator->trans('form.ForgotPassword.email.constraints.callback_1', [], 'aolr_user'));
                        }
                    })
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

            $this->userManager->sendForgotPasswordEmail($user);
            return $this->redirectToRoute('user_confirmation', [
                'email' => $email,
                'type' => 'reset'
            ]);
        }

        return $this->render('@AolrUser/security/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm-{type}/{email}", name="user_confirmation",
     *     requirements={"type":"^(reset|registration)$"}
     * )
     * @param string $email
     * @param string $type
     *
     * @return Response
     * @throws \Exception
     */
    public function confirmationMessage(string $email, string $type): Response
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (empty($user)) {
            throw new \Exception('Can not found user by email: ' . $email);
        }
        $message = $this->translator->trans('confirmation.message', [], 'aolr_user');
        switch ($type) {
            case 'reset':
                $message = $this->translator->trans('confirmation.message_reset', [], 'aolr_user');
                break;
            case 'registration':
                $message = $this->translator->trans('confirmation.message_registration', [], 'aolr_user');
                break;
        }

        return $this->render('@AolrUser/security/confirmation_message.html.twig', [
            'user' => $user,
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * @Route("/re-send-verify-email/{type}", name="user_resend_verify_email")
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function reSendValidationEmail(Request $request, string $type='registration'): JsonResponse
    {
        $email = $request->get('email', '');
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (empty($user)) {
            return $this->json([
                'code' => 500,
                'message' => 'User not found with email: ' . $email
            ]);
        }
        if ($type == 'registration' && $user->getIsVerified()) {
            return $this->json(['code' => 500, 'message' => 'User has been verified, you can login directly.']);
        }

        if ($type == 'registration') {
            $this->userManager->sendVerificationEmail($user);
        }
        if ($type == 'reset') {
            $this->userManager->sendForgotPasswordEmail($user);
        }

        return $this->json(['code' => 200, 'message' => 'Email sent out, please check.']);
    }

    /**
     * @Route("/validate-email/{key}", name="user_email_validate", requirements={"key":"\S{32}"})
     * @param string $key
     *
     * @return RedirectResponse
     * @throws \Exception
     */
    public function validateEmail(string $key): RedirectResponse
    {
        /** @var UserValidation $item */
        $item = $this->em->getRepository(UserValidation::class)->findOneBy(['hashKey' => $key]);
        if (empty($item) || !$item->getIsValid()) {
            $this->addFlash('alert-error', 'Can not find user account validation item or invalid key: ' . $key);
            return $this->redirectToRoute('app_login');
        }

        $user = $item->getUser();
        $user->setIsVerified(true);
        $item->setIsValid(false);
        $this->em->flush();

        $this->addFlash('alert-success', 'You email has been verified, and now you can login directly.');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/reset-password/{key}", name="user_reset_password", requirements={"key":"\S{32}"})
     * @param Request $request
     * @param UserPasswordHasherInterface $encoder
     * @param string $key
     *
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request, UserPasswordHasherInterface $encoder, string $key): Response
    {
        /** @var ResetPassword $item */
        $item = $this->em->getRepository(ResetPassword::class)->findOneBy(['hashKey' => $key], ['id' => 'DESC']);
        if (empty($item) || !$item->getIsValid() || time() - $item->getCreatedDate()->getTimestamp() >3600) {
            $this->addFlash('alert-error', 'Can not found reset password key or expired: ' . $key);
            return $this->redirectToRoute('app_login');
        }
        $user = $item->getUser();

        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->hashPassword($user, $form->get('password')->getData()));

            $item->setIsValid(false);
            $this->em->flush();

            $this->addFlash('alert-success', 'You password has been reset, you can login with new password now.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('@AolrUser/security/reset_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
