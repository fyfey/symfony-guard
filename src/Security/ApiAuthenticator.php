<?php

namespace App\Security;

use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    public function supports(Request $request)
    {
        return $request->isMethod('POST') && $request->getPathInfo() === '/login';
    }
    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $request->getSession()->set(Security::LAST_USERNAME, $request->get('email'));
        return [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$credentials['email'] || !$credentials['password']) {
            return;
        }

        // if a User object, checkCredentials() is called
        return new User($credentials['email'], 'fyfe.setllabs.io', ['ROLE_USER']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return $credentials['email'] == 'stu@example.com' && $credentials['password'] == 'password1';
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $sessionKey = sprintf('_security.%s.target_path', $providerKey);
        $redirectPath = $request
            ->getSession()
            ->get($sessionKey) ?? '/';
        $token->setAttribute('labs_uri', 'bob.setllabs.io');
        return new RedirectResponse($redirectPath);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse('/login');
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}