<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutSuccess',
        ];
    }

    public function onLogoutSuccess(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $defaultLogoutUrl = $this->urlGenerator->generate('app_login');

        // URL z którego przyśliśmy
        $referer = $request->headers->get('referer') ?? '';

        if($referer && parse_url($referer, PHP_URL_HOST) === $request->getHost()) {
            $refererPath = parse_url($referer, PHP_URL_PATH);

            $privatePath = [
                '/account',
                '/comment',
                '/recipe',
                '/favorite',
                '/profile/following',
            ];

            foreach ($privatePath as $path) {
                if (str_starts_with($refererPath, $path)) {
                    $event->setResponse(new RedirectResponse($defaultLogoutUrl));
                    return;
                }
            }
            $event->setResponse(new RedirectResponse($referer));
            return;
        }
        $event->setResponse(new RedirectResponse($defaultLogoutUrl));

    }
}
