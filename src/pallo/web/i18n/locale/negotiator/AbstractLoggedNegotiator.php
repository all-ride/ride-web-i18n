<?php

namespace pallo\web\i18n\locale\negotiator;

use pallo\library\dependency\DependencyInjector;
use pallo\library\i18n\locale\negotiator\Negotiator;
use pallo\library\i18n\locale\LocaleManager;
use pallo\library\log\Log;

/**
 * Negotiator that determines which locale should be used based on the locale
 * of the route
 */
abstract class AbstractLoggedNegotiator implements Negotiator {

    /**
     * Source for the log messages
     * @var string
     */
    const LOG_SOURCE = 'i18n';

    /**
     * Instance of the dependency injector
     * @var pallo\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Instance of the log
     * @var pallo\library\log\Log
     */
    protected $log;

    /**
     * Constructs a new route negotiator
     * @param pallo\library\dependency\DependencyInjector $dependencyInjector
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Sets the instance of the log
     * @param pallo\library\log\Log $log
     * @return null
     */
    public function setLog(Log $log) {
        $this->log = $log;
    }

    /**
     * Gets the current request
     * @return pallo\library\mvc\Request
     */
    protected function getRequest() {
        return $this->dependencyInjector->get('pallo\\library\\mvc\\Request');
    }

}