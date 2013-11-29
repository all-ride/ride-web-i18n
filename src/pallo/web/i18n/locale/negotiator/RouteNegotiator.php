<?php

namespace pallo\web\i18n\locale\negotiator;

use pallo\library\i18n\locale\LocaleManager;

/**
 * Negotiator that determines which locale should be used based on the locale
 * of the route
 */
class RouteNegotiator extends AbstractLoggedNegotiator {

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param pallo\library\i18n\locale\LocaleManager $manager The locale manager
     * @return null|pallo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager) {
        $request = $this->getRequest();
        if (!$request) {
            return null;
        }

        $route = $request->getRoute();
        if (!$route) {
            return null;
        }

        $locale = $route->getLocale();

        if ($locale && $manager->hasLocale($locale)) {
            if ($this->log) {
                $this->log->logDebug('Loaded locale from route', $locale, self::LOG_SOURCE);
            }

            return $manager->getLocale($locale);
        } elseif ($this->log) {
            $this->log->logDebug('No available locale found in route', $locale, self::LOG_SOURCE);
        }

        return null;
    }

}