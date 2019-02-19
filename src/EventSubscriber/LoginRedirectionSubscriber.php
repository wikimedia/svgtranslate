<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This event subscriber redirects users back to the translate page from which they logged in from,
 * by storing the current SVG filename in the session and then checking for it after the OAuth
 * redirection to 'home' happens (from the ToolforgeBundle).
 */
class LoginRedirectionSubscriber implements EventSubscriberInterface
{

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    protected const SESSION_KEY = 'current_filename';

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Store the current SVG filename in the session.
     * @param FilterResponseEvent $event
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $filename = $event->getRequest()->get('filename');
        if ($filename) {
            $event->getRequest()->getSession()->set(static::SESSION_KEY, $filename);
        }
    }

    /**
     * Get the current SVG filename from the session and redirect iff we're currently being
     * redirected by the AuthController in the bundle.
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        // Only act on the ToolforgeBundle's OAuth callback.
        if ('toolforge_oauth_callback' !== $event->getRequest()->get('_route')) {
            return;
        }

        // See if the 'current_filename' is set in the session
        // (this is done in the TranslateController only).
        $currentFilename = $event->getRequest()->getSession()->get(static::SESSION_KEY);
        if (!$currentFilename) {
            return;
        }

        // Redirect to the stored filename.
        $url = $this->urlGenerator->generate('translate', ['filename' => $currentFilename]);
        $event->setResponse(new RedirectResponse($url));
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'onKernelController',
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
