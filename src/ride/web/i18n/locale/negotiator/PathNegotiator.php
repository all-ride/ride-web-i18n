<?php

namespace ride\web\i18n\locale\negotiator;

use ride\library\i18n\locale\LocaleManager;

/**
 * Negotiator that determines which locale should be used based on the locale
 * of the route
 */
class PathNegotiator extends AbstractLoggedNegotiator {

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param \ride\library\i18n\locale\LocaleManager $manager The locale manager
     * @return null| \ride\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager) {
        $request = $this->getRequest();
        if (!$request) {
            return null;
        }

        $path = trim($request->getBasePath(), '/');
        if (!$path) {
            return null;
        }

        $pathTokens = explode('/', $path);
        $token = array_shift($pathTokens);

        if ($manager->hasLocale($token)) {
            if ($this->log) {
                $this->log->logDebug('Loaded locale from path', $path, self::LOG_SOURCE);
            }

            return $manager->getLocale($token);
        } elseif ($this->log) {
            $this->log->logDebug('No available locale found in path', $path, self::LOG_SOURCE);
        }

        return null;
    }

}