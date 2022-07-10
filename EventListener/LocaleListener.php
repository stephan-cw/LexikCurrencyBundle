<?php

namespace Lexik\Bundle\CurrencyBundle\EventListener;

use Lexik\Bundle\CurrencyBundle\Currency\FormatterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class LocaleListener implements EventSubscriberInterface
{
    /** @var FormatterInterface */
    private $formatter;

    /**
     * @param FormatterInterface $formatter
     */
    public function __construct(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                // must be registered before the default Locale listener
                ['setCurrencyFormatterLocale', 17]
            ],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function setCurrencyFormatterLocale(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $this->formatter->setLocale($request->getLocale());
    }
}
