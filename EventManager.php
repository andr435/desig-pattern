<?php

namespace Viber\core;

use Viber\core\interfaces\PublisherInterface;
use Viber\core\interfaces\SubscriberInterface;

/**
 * Class EventManager
 * @package Viber\core
 */
class EventManager {

    protected static $instance = null;

    /**
     * Map of registered listeners.
     * <event> => <listeners>
     * @var array
     */
    private $_listeners = [];

    /**
     * Singleton - can't create constructor outside
     */
    protected function __construct(){}

    /**
     * Singleton - can't be cloned
     */
    protected function __clone(){}

    /**
     * Get Object instance
     * @return EventManager
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * Add event to listeners list
     *
     * @param string $eventName
     * @param array $callback
     */
    public function addEventListener($eventName, $callback)
    {
        $hash = spl_object_hash ($callback[0]);
        //prevent duplicate
        if(!isset($this->_listeners[$eventName][$hash])) {
            $this->_listeners[$eventName][$hash] = $callback;
        }
    }

    /**
     * Remove an event listener from the specified events.
     *
     * @param string $eventName
     * @param array $listener
     */
     public function removeEventListener($eventName, $listener)
     {

         $hash = spl_object_hash ($listener[0]);
         if (isset($this->_listeners[$eventName][$hash])) {
             unset($this->_listeners[$eventName][$hash]);
         }
     }

    /**
     * Dispatch event to subscribers
     *
     * @param string $eventName
     * @param PublisherInterface $param
     */
    public function dispatchEvent($eventName, PublisherInterface $param)
    {
        if (isset($this->_listeners[$eventName])) {
            foreach ($this->_listeners[$eventName] as $listener) {
                call_user_func_array($listener, array($param));
            }
        }
    }

    /**
     * Add a new subscriber and add it to listeners list.
     *
     * @param SubscriberInterface $sub
     */
    public function addEventSubscriber(SubscriberInterface $sub)
    {
        $listeners = $sub->getSubscribedEvents();

        foreach ($listeners as $eventName => $callback)
        {
            $this->addEventListener($eventName, array($sub, $callback));
        }
    }

    /**
     * Gets the listeners of a specific event or all listeners.
     *
     * @param string $eventName The name of the event.
     * @return array The event listeners for the specified event, or all event listeners.
     */
    public function getListeners($eventName = '')
    {
          return !empty($eventName) ? $this->_listeners[$eventName] : $this->_listeners;
    }

    /**
    * Checks whether an event has any registered listeners.
    *
    * @param string $eventName
    * @return boolean TRUE if the specified event has any listeners, FALSE otherwise.
    */
    public function hasListeners($eventName)
    {
         return isset($this->_listeners[$eventName]) && $this->_listeners[$eventName];
    }

    /**
     * Reset Event Manager, remove all listeners
     *
     * @return bool
     */
    public function reset()
    {
        $this->_listeners = [];
        return true;
    }


}
