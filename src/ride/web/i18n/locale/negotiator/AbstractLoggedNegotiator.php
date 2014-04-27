<?php

namespace ride\web\i18n\locale\negotiator;

use ride\library\dependency\DependencyInjector;
use ride\library\i18n\locale\negotiator\Negotiator;
use ride\library\log\Log;

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
     * @var \ride\library\dependency\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Instance of the log
     * @var \ride\library\log\Log
     */
    protected $log;

    /**
     * Constructs a new route negotiator
     * @param \ride\library\dependency\DependencyInjector $dependencyInjector
     * @return null
     */
    public function __construct(DependencyInjector $dependencyInjector) {
        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Sets the instance of the log
     * @param \ride\library\log\Log $log
     * @return null
     */
    public function setLog(Log $log) {
        $this->log = $log;
    }

    /**
     * Gets the current request
     * @return \ride\library\mvc\Request
     */
    protected function getRequest() {
        return $this->dependencyInjector->get('ride\\library\\mvc\\Request');
    }

}