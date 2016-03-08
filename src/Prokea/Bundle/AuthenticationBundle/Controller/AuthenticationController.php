<?php
namespace Prokea\Bundle\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class AuthenticationController extends Controller
{
    public function authorizeAction(Request $request)
    {
        // We only handle non-authorized scope here.
        try {
            return $this->get('prokea.oauth2_controller')->authorizeAction($request);
        } catch (InvalidScopeException $exception) {
            echo 'error';
            $message = unserialize($exception->getMessage());
            if ($message['error_description'] !== 'The requested scope is invalid.') {
                throw $exception;
            }
        }

        // Fetch parameters, which already checked.
        $clientId = $request->query->get('client_id');
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
        $scope = preg_split('/\s+/', $request->query->get('scope', ''));

        // Create form.
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);

        // Save authorized scope if submitted by POST.
        if ($form->isValid()) {
            $modelManagerFactory = $this->get('authbucket_oauth2.model_manager.factory');
            $authorizeManager = $modelManagerFactory->getModelManager('authorize');

            // Update existing authorization if possible, else create new.
            $authorize = $authorizeManager->readModelOneBy([
                'clientId' => $clientId,
                'username' => $username,
            ]);
            if ($authorize === null) {
                $class = $authorizeManager->getClassName();
                $authorize = new $class();
                $authorize->setClientId($clientId)
                    ->setUsername($username)
                    ->setScope((array) $scope);
                $authorize = $authorizeManager->createModel($authorize);
            } else {
                $authorize->setClientId($clientId)
                    ->setUsername($username)
                    ->setScope(array_merge((array) $authorize->getScope(), $scope));
                $authorizeManager->updateModel($authorize);
            }

            // Back to this path, with original GET parameters.
            return $this->redirect($request->getRequestUri());
        }

        // Display the form.
        $authorizationRequest = $request->query->all();

        return $this->render('TestBundle:demo:authorize.html.twig', [
            'client_id' => $clientId,
            'username' => $username,
            'scopes' => $scope,
            'form' => $form->createView(),
            'authorization_request' => $authorizationRequest,
        ]);
    }
    
    public function requestCodeAction(Request $request)
    {
        $session = $request->getSession();
//        var_dump($session);
//
//        $_username = $session->get('_username', substr(md5(uniqid(null, true)), 0, 8));
//        $_password = $session->get('_password', substr(md5(uniqid(null, true)), 0, 8));
//
//        $session->set('_username', $_username);
//        $session->set('_password', $_password);

//        $userManager = $this->get('authbucket_oauth2.model_manager.factory')->getModelManager('user');
//        $user = $userManager->createUser()
//            ->setUsername($_username)
//            ->setPassword($_password)
//            ->setRoles([
//                'ROLE_USER',
//            ]);
//        $userManager->updateUser($user);

        $parameters = [
            'response_type' => 'code',
            'client_id' => 'authorization_code_grant',
            'redirect_uri' => $request->getUriForPath('/oauth/response_type/code'),
            'scope' => 'demoscope1',
            'state' => $session->getId(),
        ];
        
        $url = Request::create($request->getUriForPath('/oauth/authorize'), 'GET', $parameters)->getUri();

        return $this->redirect($url);
    }
    
    public function responseTypeCodeAction(Request $request)
    {
        $authorizationResponse = $request->query->all();

        $tokenPath = $this->get('router')->generate('demo_grant_type_authorization_code', [
            'code' => $authorizationResponse['code'],
        ]);

        return $this->render('ProkeaAuthenticationBundle:response_type:code.html.twig', [
            'authorization_response' => $authorizationResponse,
            'token_path' => $tokenPath,
        ]);
    }
    
    public function grantTypeAuthorizationCodeAction(Request $request)
    {
        $parameters = [
            'grant_type' => 'authorization_code',
            'code' => $request->query->get('code'),
            'redirect_uri' => $request->getUriForPath('/demo/response_type/code'),
            'client_id' => 'authorization_code_grant',
            'client_secret' => 'uoce8AeP',
        ];
        $server = [];
        $client = new Client($this->get('kernel'));
        $crawler = $client->request('POST', '/api/oauth2/token', $parameters, [], $server);
        $accessTokenResponse = json_decode($client->getResponse()->getContent(), true);
        $accessTokenRequest = get_object_vars($client->getRequest());

        $modelPath = $this->get('router')->generate('demo_resource_type_model', [
            'access_token' => $accessTokenResponse['access_token'],
        ]);
        $debugPath = $this->get('router')->generate('demo_resource_type_debug_endpoint', [
            'access_token' => $accessTokenResponse['access_token'],
        ]);
        $refreshPath = $this->get('router')->generate('demo_grant_type_refresh_token', [
            'username' => 'authorization_code_grant',
            'password' => 'uoce8AeP',
            'refresh_token' => $accessTokenResponse['refresh_token'],
        ]);

        return $this->render('TestBundle:demo/grant_type:authorization_code.html.twig', [
            'access_token_response' => $accessTokenResponse,
            'access_token_request' => $accessTokenRequest,
            'model_path' => $modelPath,
            'debug_path' => $debugPath,
            'refresh_path' => $refreshPath,
        ]);
    }
}

