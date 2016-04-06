<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 6/4/16
 * Time: 17:47
 */

namespace AppBundle\Service\Mailer;


use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

class MailerRepository
{

    protected $mailer;
    protected $router;
    protected $templating;
    protected $parameters;

    public function __construct($mailer, UrlGeneratorInterface  $router, \Twig_Environment $templating, array $parameters)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters;
    }

    /**
     * Send an email to a user to confirm the account creation
     *
     * @param User $user
     *
     * @return void
     */
    public function sendConfirmationEmailMessage(User $user)
    {
        $template = $this->parameters['confirmation.template'];
        $url = $this->router->generate('confirmation', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        $params = array(
            'user' => $user,
            'confirmationUrl' =>  $url
        );

        $this->sendEmailMessage($template, $params, $this->parameters['from_email'], $user->getEmail());
    }



    /**
     * @param string $templateName
     * @param array  $context
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($templateName, $context, $fromEmail, $toEmail)
    {

        $context = $this->templating->mergeGlobals($context);
        $template = $this->templating->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);
        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html');
        } else {
            $message->setBody($textBody, '');
        }
        $this->mailer->send($message);
    }
}