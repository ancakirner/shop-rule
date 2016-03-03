<?php
namespace Prokea\Bundle\AuthenticationBundle\Controller;

//use AuthBucket\OAuth2\Exception\InvalidScopeException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpKernel\Client;
use Symfony\Component\Security\Core\Security;

class LoginController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        }
        
        $_username = $session->get('_username');
        $_password = $session->get('_password');
        
        return $this->render('ProkeaAuthenticationBundle:Login:login.html.twig', [
                'error' => $error,
                '_username' => $_username,
                '_password' => $_password,
        ]);
    }
}