<?php

namespace Zoolanders\Framework\Container;

use Auryn\Injector;
use Joomla\Registry\Registry;
use Zoolanders\Framework\Autoloader;
use Zoolanders\Framework\Factory\Factory;
use Zoolanders\Framework\Dispatcher\Dispatcher;
use Zoolanders\Framework\Dispatcher\Exception;
use Zoolanders\Framework\Response\ResponseInterface;

defined('_JEXEC') or die;

/**
 * Class Container
 * @package Zoolanders\Framework\Container
 *
 * @property-read   \Zoolanders\Framework\Service\Inflector $inflector
 * @property-read   \Zoolanders\Framework\Service\Filesystem $filesystem
 * @property-read   \Zoolanders\Framework\Service\Path $path
 * @property-read   \Zoolanders\Framework\Container\Nested\System $system
 * @property-read   \Zoolanders\Framework\Container\Nested\Assets $assets
 * @property-read   \Zoolanders\Framework\Factory\Factory $factory
 * @property-read   \Zoolanders\Framework\Service\Zoo $zoo
 * @property-read   \Zoolanders\Framework\Service\Database $db
 * @property-read   \Zoolanders\Framework\Service\Database $database
 * @property-read   \Zoolanders\Framework\Event\Dispatcher $event
 * @property-read   \Zoolanders\Framework\Service\Date $date
 * @property-read   \Zoolanders\Framework\Request\Request $input
 * @property-read   \Zoolanders\Framework\Request\Request $request
 * @property-read   \Zoolanders\Framework\Service\Params $params
 * @property-read   \Zoolanders\Framework\Service\Joomla $joomla
 * @property-read   \Zoolanders\Framework\Service\System\Document $document
 * @property-read   \Zoolanders\Framework\Service\Environment $environment
 * @property-read   \Zoolanders\Framework\Service\Installation $installation
 * @property-read   \Zoolanders\Framework\Service\Dependencies $dependencies
 * @property-read   \Zoolanders\Framework\Service\Route $route
 * @property-read   \Zoolanders\Framework\Service\Crypt $crypt
 * @property-read   \Zoolanders\Framework\Service\Data $data
 * @property-read   \Zoolanders\Framework\Service\Link $link
 * @property-read   \Zoolanders\Framework\Service\Cache $cache
 * @property-read   \Zoolanders\Framework\Service\User $user
 * @property-read   \Zoolanders\Framework\Service\Zip $zip
 */
class Container {
    /**
     * Namespaces roots for zoolanders
     */
    const ROOT_NAMESPACE = 'Zoolanders\\';

    /**
     * Framework special namespace
     */
    const FRAMEWORK_NAMESPACE = 'Zoolanders\\Framework\\';

    /**
     * The container instance
     * @var Container
     */
    protected static $container = null;

    /**
     * The path to the config file
     * @var string
     */
    protected static $configFile = 'libraries/zoolanders/Framework/config.json';

    /**
     * @var Registry
     */
    protected $config;

    /**
     * @var Autoloader
     */
    public $loader;

    /**
     * @var Factory
     */
    public $factory;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var array
     */
    protected $registeredExtensions;

    /**
     * Container constructor.
     */
    protected function __construct () {
        $this->loader = Autoloader::getInstance();
        $this->injector = new Injector();
        $this->factory = new Factory($this);
        $this->event = new \Zoolanders\Framework\Event\Dispatcher($this);
    }

    /**
     * @param $name
     * @param array $args
     * @return mixed
     */
    public function make ($name, $args = []) {
        return $this->injector->make($name, $args);
    }

    /**
     * @param $name
     * @return Injector
     */
    public function share ($name) {
        return $this->injector->share($name);
    }

    /**
     * @param $callable
     * @param array $args
     */
    public function execute ($callable, $args = []) {
        return $this->injector->execute($callable, $args);
    }

    /**
     * @param $name
     * @param null $path
     * @param null $namespace
     */
    public function registerExtension ($name, $path = null, $namespace = null) {
        // Autodetect path
        if (!$path) {
            $componentName = $name;

            if (stripos($componentName, 'com_') !== 0) {
                $componentName = 'com_' . $componentName;
            }

            $path = JPATH_ADMINISTRATOR . '/components/' . strtolower($componentName);
        }

        if (!$namespace) {
            $namespace = Container::ROOT_NAMESPACE . ucfirst(strtolower($name)) . '\\';
        }

        $this->registeredExtensions[strtolower($name)][] = $namespace;
        $this->loader->addPsr4($namespace, $path . '/');
    }

    /**
     * @param $extension
     * @return array|mixed
     */
    public function getRegisteredExtensionNamespaces ($extension) {
        if (!isset($this->registeredExtensions[$extension])) {
            return [];
        }

        return $this->registeredExtensions[$extension];
    }

    /**
     * Singleton pattern
     * @return Container
     */
    public static function &getInstance () {
        if (self::$container) {
            return self::$container;
        }

        $container = new Container();
        $container->share($container->factory);
        $container->share($container->event);

        // get the config file
        $config = new Registry();
        $config->loadFile(JPATH_SITE . '/' . self::$configFile);

        // Load the configuration file
        $container->loadConfig($config);

        self::$container = $container;

        return self::$container;
    }

    /**
     * Load the configuration
     * @param Registry $config
     */
    public function loadConfig (Registry $config) {
        // Merge it with the current config
        if ($this->config) {
            $this->config->merge($config, true);
        } else {
            $this->config = $config;
        }

        // load the services classes from the config file
        $services = $config->get('services', []);
        $this->loadServices($services);

        try {
            // Bind any listener for events
            $services = $config->get('events', []);
            $this->event->bindEvents($services);
        } catch (\InvalidArgumentException $e) {
            // Ignore the event argument exception, we know that, thank you very much
        }
    }

    /**
     * Load the service into the DI Container
     * @param $services
     */
    protected function loadServices ($services) {
        // Load the services
        foreach ($services as $name => $class) {
            $this->share($class);
        }
    }

    /**
     * Act as service locator. BAD
     * @param $name
     * @return mixed
     */
    public function __get ($name) {
        $services = $this->config->get('services', []);
        if ($class = $services->$name) {
            return $this->make($class);
        }
    }

    /**
     * @param string $default controller name
     * @throws Exception\BadResponseType
     * @throws Exception\ControllerNotFound
     */
    public function dispatch ($default_controller = null) {
        $event = $this->event->create('Dispatcher\BeforeDispatch');
        $this->event->trigger($event);

        $dispatcher = new Dispatcher($this);
        $dispatcher->setDefaultController($default_controller);

        $response = $this->injector->execute([$dispatcher, 'dispatch']);

        $event = $this->event->create('Dispatcher\AfterDispatch');
        $this->event->trigger($event);

        if ($response instanceof ResponseInterface) {
            $response->send();
            return;
        }

        throw new Exception\BadResponseType();
    }
}
