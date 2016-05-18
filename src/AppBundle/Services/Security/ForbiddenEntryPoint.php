<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 18/4/16
 * Time: 16:17
 */

namespace AppBundle\Services\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ForbiddenEntryPoint implements AuthenticationEntryPointInterface
{

    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response($this->twig->render('Exception/error403.whole.html.twig'));
    }

}