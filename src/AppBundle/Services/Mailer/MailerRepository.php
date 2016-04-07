<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 6/4/16
 * Time: 17:47
 */

namespace AppBundle\Services\Mailer;


use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailerRepository
{

    protected $mailer;
    protected $router;
    protected $templating;
    protected $parameters;

    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface  $router, \Twig_Environment $templating, array $parameters)
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
     * @param  $token
     *
     * @return void
     */
    public function sendConfirmationEmailMessage(User $user, $token)
    {
        $template = $this->parameters['confirmation.template'];
        $url = $this->router->generate('confirmation', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        $params = array(
            'user' => $user,
            'url' =>  $url
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
        $htmlBody = $template->renderBlock('body_html', $context);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);
            $message->setBody($htmlBody, 'text/html');

        $this->mailer->send($message);
    }
}