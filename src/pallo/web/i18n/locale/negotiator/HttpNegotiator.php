<?php

namespace pallo\web\i18n\locale\negotiator;

use pallo\library\i18n\locale\LocaleManager;

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
            $localeLocales = explode(',', $locale);
            foreach ($localeLocales as $locale) {
                $this->disabledLocales[trim($locale)] = true;
            }
        }
    }

    /**
     * Determines which locale to use, based on the HTTP Accept-Language header.
     * @param pallo\library\i18n\locale\LocaleManager $manager The locale manager
     * @return null|pallo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager) {
        $request = $this->getRequest();
        if (!$request) {
            if ($this->log) {
                $this->log->logDebug('Can\'t determine locale because there is no request', null, self::LOG_SOURCE);
            }

            return null;
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

            if (!isset($this->disabledLocales[$locale]) && $manager->hasLocale($locale)) {
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