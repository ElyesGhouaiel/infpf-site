<?php

namespace App\Security;

use App\Repository\UserRepository;
use Karser\Recaptcha3Bundle\Services\IpResolverInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private ?ReCaptcha $recaptcha = null,
        private ?IpResolverInterface $ipResolver = null,
        private string $recaptchaSecretKey = ''
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $usernameOrEmail = $request->request->get('email', '');
        
        // Validation reCAPTCHA v3
        $captchaToken = $request->request->get('captcha_token', '');
        if ($captchaToken && $this->recaptcha) {
            $response = $this->recaptcha->verify($captchaToken, $this->ipResolver ? $this->ipResolver->resolveIp($request) : null);
            if (!$response->isSuccess() || $response->getScore() < 0.5) {
                throw new CustomUserMessageAuthenticationException('Échec de la vérification reCAPTCHA. Veuillez réessayer.');
            }
        }

        $request->getSession()->set(Security::LAST_USERNAME, $usernameOrEmail);

        return new Passport(
            new UserBadge($usernameOrEmail, function($userIdentifier) {
                return $this->userRepository->findByUsernameOrEmail($userIdentifier);
            }),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        
        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
