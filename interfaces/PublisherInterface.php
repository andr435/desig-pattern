<?php

namespace Viber\core\interfaces;

/**
 * Part of observer
 * Interface PublisherInterface
 * @package Viber\core\interfaces
 */
interface PublisherInterface {
    public function notify($eventName);
}