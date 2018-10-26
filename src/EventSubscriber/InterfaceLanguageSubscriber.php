<?php

declare(strict_types = 1);

namespace App\EventSubscriber;

use Krinkle\Intuition\Intuition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class InterfaceLanguageSubscriber implements EventSubscriberInterface
{

    /** @var Intuition */
    protected $intuition;

    public function __construct(Intuition $intuition)
    {
        $this->intuition = $intuition;
    }

    /**
     * See if there's an interface language stored in the cookie
     * and set the current language if there is.
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event):void
    {
        $cookie = $event->getRequest()->cookies->get('svgtranslate');
        if (!$cookie) {
            return;
        }
        $cookieValue = json_decode($cookie);
        if (isset($cookieValue->interfaceLang)) {
            $this->intuition->setLang($cookieValue->interfaceLang);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents():array
    {
        return [
           'kernel.controller' => 'onKernelController',
        ];
    }
}
