<?php

namespace Aolr\UserBundle\Service;

use Aolr\UserBundle\Entity\EmailCode;
use Aolr\UserBundle\Entity\ResetPassword;
use Aolr\UserBundle\Entity\User;
use Aolr\UserBundle\Entity\UserValidation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserManager
{
    const MAIL_VALIDATION           = 'validation';
    const MAIL_FORGOT_PASSWORD      = 'forgot_password';
    const MAIL_VERIFY_CODE          = 'verify_code';

    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $emailsConfig;

    public function __construct(
        ConfigManager $configManager,
        MailerInterface $mailer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        RouterInterface $router,
        RequestStack $requestStack, Environment $twig)
    {
        $this->configManager    = $configManager;
        $this->mailer           = $mailer;
        $this->validator        = $validator;
        $this->em               = $em;
        $this->router           = $router;
        $this->request          = $requestStack->getCurrentRequest();
        $this->twig             = $twig;
        $this->emailsConfig     = $configManager->getEmails();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function sendVerificationEmail(User $user)
    {
        $validation = $this->em->getRepository(UserValidation::class)->findOneBy(['user' => $user]);
        if (empty($validation)) {
            $validation = new UserValidation();
            $validation->setUser($user)->setHashKey(md5(uniqid(rand(), true)));
            $this->em->persist($validation);
            $this->em->flush();
        }

        $this->formatAndSend([
            'to' => $user->getEmail(),
            'subject' => $this->emailsConfig[self::MAIL_VALIDATION]['subject'] ?? ('[' . $this->configManager->getPublisherName() . '] User Account Verification'),
            'body' => $this->twig->render($this->emailsConfig[self::MAIL_VALIDATION]['body'] ?? '@AolrUser/email/registration_validation.html.twig', [
                'user_validate_link' => $this->router->generate('user_email_validate', [
                    'key' => $validation->getHashKey()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'publisher' => [
                    'name' => $this->configManager->getPublisherName(), 'host' => $this->request->server->get('REQUEST_SCHEME') . '://' . $this->request->server->get('HTTP_HOST')
                ]
            ])
        ], self::MAIL_VALIDATION);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendForgotPasswordEmail(User $user)
    {
        $resetPassword = $this->em->getRepository(ResetPassword::class)->findOneBy(['user' => $user->getId(), 'isValid' => true]);
        if (empty($resetPassword)) {
            $resetPassword = new ResetPassword();
            $resetPassword->setUser($user)->setHashKey(md5(uniqid(rand(), true)));
            $this->em->persist($resetPassword);
        }
        $resetPassword->setCreatedDate(new \DateTime());
        $this->em->flush();

        $this->formatAndSend([
            'to' => $user->getEmail(),
            'subject' => $this->emailsConfig[self::MAIL_FORGOT_PASSWORD]['subject'] ?? ('[' . $this->configManager->getPublisherName() . '] User Account - Forgot Password'),
            'body' => $this->twig->render($this->emailsConfig[self::MAIL_FORGOT_PASSWORD]['body'] ??  '@AolrUser/email/forget_password.html.twig', [
                'user_reset_password_link' => $this->router->generate('user_reset_password', [
                    'key' => $resetPassword->getHashKey()
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'publisher' => [
                    'name' => $this->configManager->getPublisherName(), 'host' => $this->request->server->get('REQUEST_SCHEME') . '://' . $this->request->server->get('HTTP_HOST')
                ]
            ])
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendCode(string $email)
    {
        $errors = $this->validator->validate($email, [
            new NotBlank(),
            new Email()
        ]);

        if ($errors->count() > 0) {
            throw new \Exception($errors[0]->getMessage());
        }

        $emailCode = $this->em->getRepository(EmailCode::class)->findOneBy(['email' => $email]);
        if (empty($emailCode)) {
            $emailCode = new EmailCode();
            $emailCode->setCode(mt_rand(100000, 999999))->setEmail($email);
            $this->em->persist($emailCode);
        }

        $emailCode->setSendTimes($emailCode->getSendTimes() + 1);
        $this->em->flush();

        $this->formatAndSend([
            'to' => $email,
            'subject' => $this->emailsConfig[self::MAIL_VERIFY_CODE]['subject'] ?? ('[' . $this->configManager->getPublisherName() . '] Email Verification Code.'),
            'body' => $this->twig->render($this->emailsConfig[self::MAIL_VERIFY_CODE]['body'] ?? '@AolrUser/email/send_code.html.twig', [
                'code' => $emailCode->getCode()
            ])
        ], self::MAIL_VERIFY_CODE);
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function formatAndSend(array $context, string $type='')
    {
        $email = new \Symfony\Component\Mime\Email();
        if (empty($context['to']) || empty($context['subject']) || empty($context['body'])) {
            throw new \Exception('No From or To found when sending email: ' . $type);
        }

        if (!empty($emailsConfig['transport'])) {
            $email->getHeaders()->addTextHeader('X-Transport', $this->emailsConfig['transport']);
        }
        $email->from($this->emailsConfig['from'] ?? 'no reply <no-reply@aolr.pub>');
//        $email->replyTo(Address::create($context['from']));

        $getAddress = function ($input) {
            return is_array($input) ? $input : [$input];
        };
        $tos = $getAddress($context['to'] ?? []);
        foreach ($tos as $to) {
            $email->addTo(Address::create($to));
        }

        $email->subject($context['subject']);
        $email->text($context['body']);

        $this->mailer->send($email);
    }
}
