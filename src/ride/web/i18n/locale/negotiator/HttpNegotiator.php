<?php

namespace ride\web\i18n\locale\negotiator;

use ride\library\i18n\locale\LocaleManager;
use ride\library\mvc\Request as MvcRequest;
use ride\library\StringHelper;

/**
 * Negotiator that determines which locale should be used based on the HTTP Accept-Language
 * in the request, as described in
 * {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4 RFC 2616 section 14.4}.
 *
 * eg. es,en-us;q=0.7,ar-lb;q=0.3
 */
class HttpNegotiator extends AbstractLoggedNegotiator {

    /**
     * Separator of a locale between the language and the territory
     * @var string
     */
    const SEPARATOR_TERRITORY = '-';

    /**
     * Array with disabled locales
     * @var array
     */
    private $disabledLocales;

    /**
     * Array with the paths to ignore the disabled locales for
     * @var array
     */
    private $ignoreDisabledLocales = [];

    /**
     * Disable a locale for this negotiator
     * @param string|array $locales Locale code or array with locale codes
     * @return null
     */
    public function setDisabledLocales($locales) {
        if (!is_array($locales)) {
            $locales = array($locales);
        }

        if ($this->disabledLocales === null) {
            $this->disabledLocales = array();
        }

        foreach ($locales as $locale) {
            if (is_null($locale)) {
                continue;
            }
            $localeLocales = explode(',', $locale);
            foreach ($localeLocales as $locale) {
                $this->disabledLocales[trim($locale)] = true;
            }
        }
    }

    /**
     * Sets paths to ignore the disabled locales for
     * @param string|array $paths All requested paths starting with a path in
     * this array will not use the disabled locales
     * @return null
     */
    public function setIgnorePathsForDisabledLocales($paths) {
        $this->ignoreDisabledLocales = array();

        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $this->ignoreDisabledLocales[$path] = true;
        }
    }

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param \ride\library\i18n\locale\LocaleManager $manager The locale manager
     * @return null| \ride\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager) {
        $request = $this->getRequest();
        if (!$request) {
            if ($this->log) {
                $this->log->logDebug('Can\'t determine locale because there is no request', null, self::LOG_SOURCE);
            }

            return null;
        }

        if ($request instanceof MvcRequest) {
            $path = $request->getBasePath();
        } else {
            $path = $request->getPath();
        }

        $fallbackLanguages = array();

        $acceptedLanguages = $request->getAcceptLanguage();
        foreach ($acceptedLanguages as $acceptedLanguage => $null) {
            if (strpos($acceptedLanguage, self::SEPARATOR_TERRITORY) === false) {
                $locale = strtolower($acceptedLanguage);
            } else {
                list($language, $territory) = explode(self::SEPARATOR_TERRITORY, $acceptedLanguage);
                $language = strtolower($language);
                $locale = $language . '_' . strtoupper($territory);

                $fallbackLanguages[$language] = true;
            }

            if (isset($this->disabledLocales[$locale]) && (!$this->ignoreDisabledLocales || ($this->ignoreDisabledLocales && !StringHelper::startsWith($path, array_keys($this->ignoreDisabledLocales))))) {
                if ($this->log) {
                    $this->log->logDebug('Could not use locale from Accept-Language header', $locale . ' is disabled', self::LOG_SOURCE);
                }

                continue;
            }

            if ($manager->hasLocale($locale)) {
                if ($this->log) {
                    $this->log->logDebug('Loaded locale from Accept-Language header', $locale, self::LOG_SOURCE);
                }

                return $manager->getLocale($locale);
            }
        }

        foreach ($fallbackLanguages as $locale => $null) {
            if (!isset($this->disabledLocales[$locale]) && $manager->hasLocale($locale)) {
                if ($this->log) {
                    $this->log->logDebug('Loaded locale from Accept-Language header (fallback)', $locale, self::LOG_SOURCE);
                }

                return $manager->getLocale($locale);
            }
        }

        if ($this->log) {
            $this->log->logDebug('No available locale in Accept-Language header', null, self::LOG_SOURCE);
        }

        return null;
    }

}
