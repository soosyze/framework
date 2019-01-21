<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\Stream;
use Soosyze\Components\Util\Util;
use Soosyze\Container;
use Soosyze\Router;

/**
 * Coeur de l'application, il est le ciment qui unis les modules et les services.
 * Il possède la logique de framework (le router, les configurations... ).
 *
 * @author Mathieu NOËL
 */
abstract class App
{
    /**
     * Liste des environnements.
     *
     * @var array
     */
    protected $environnement = [];

    /**
     * Instance unique de App.
     *
     * @var $self
     */
    private static $instance = null;

    /**
     * Le routeur.
     *
     * @var Router
     */
    private $router;

    /**
     * Conteneur d'injection de dépendance (CID).
     *
     * @var Container
     */
    private $container;

    /**
     * Instances des modules.
     *
     * @var array
     */
    private $modules = [];

    /**
     * Paramètres du framework.
     *
     * @var array
     */
    private $settings = [];

    /**
     * Requête courante à la création de la classe.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Liste des routes.
     *
     * @var array
     */
    private $routes = [];

    /**
     * Liste des services.
     *
     * @var array
     */
    private $services = [];

    /**
     * À la construction de notre application ont créé l'objet Request
     * pour le traiter et renvoyer une réponse.
     *
     * @param ServerRequestInterface $request Requête courante de l'application.
     */
    private function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Singleton pour une classe abstraite.
     *
     * @param ServerRequestInterface|null $request Requête courante de l'application.
     *
     * @return self Instancte unique de App.
     */
    public static function getInstance(ServerRequestInterface $request)
    {
        if (is_null(self::$instance)) {
            $class          = get_called_class();
            self::$instance = new $class($request);
        }

        return self::$instance;
    }

    /**
     * Charge les paramètres du framework.
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Revoie les paramètres du framework.
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Revoie la valeur d'un paramètre du framework ou le paramètre par défaut.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getSetting($key, $default = '')
    {
        return isset($this->settings[ $key ])
            ? $this->settings[ $key ]
            : $default;
    }

    /**
     * Initialise le routeur et le container. Charge les configurations, les routes,
     * les services et les modules. Transmet le container aux contrôleurs.
     *
     * @return $this
     */
    public function init()
    {
        $config = new Config($this->getSetting('config'), $this->getEnvironment());

        $this->container = (new Container)
            ->setConfig($config)
            ->setInstance('core', $this)
            ->setInstance('config', $config);

        $services = $this->loadServices();
        $this->container->setServices($services);

        $this->modules = $this->loadModules();
        $this->loadRoutesAndServices();

        $this->router = (new Router($this->routes, $this->modules))
            ->setRequest($this->request);

        $this->container->addServices($this->services)
            ->setInstance('router', $this->router);

        foreach ($this->modules as $module) {
            $module->setContainer($this->container);
        }

        return $this;
    }

    /**
     * Lance l'application.
     *
     * @return ResponseInterface La magie de l'application.
     */
    public function run()
    {
        $request  = clone $this->request;
        $response = new Response(404, new Stream(null));

        $this->container->callHook('app.response.before', [ &$request, &$response ]);

        if (($route = $this->router->parse($request)) && $response->getStatusCode() == 404) {
            $this->container->callHook($route[ 'key' ] . '.response.before', [
                &$request,
                &$response
            ]);

            $exec     = $this->router->execute($route, $request);
            $response = $this->parseResponse($exec);

            $this->container->callHook($route[ 'key' ] . '.response.after', [
                $this->request,
                &$response
            ]);
        }
        $this->container->callHook('app.' . $response->getStatusCode(), [
            $this->request,
            &$response
        ]);

        $this->container->callHook('app.response.after', [ $this->request, &$response ]);

        return $response;
    }

    /**
     * Cherche l'instance d'un service dans le container.
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $key Nom du service.
     *
     * @return object Service du container.
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * Ajoute un service au container.
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $key Nom du service.
     * @param string $srv Instance du service.
     *
     * @return $this
     */
    public function set($key, $srv)
    {
        $this->container->set($key, $srv);

        return $this;
    }

    /**
     * Appelle un hook (trigger/middelware).
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $name Nom du hook à déclencher.
     * @param array  $args Arguments de la fonction de rappelle.
     *
     * @return mixed Résultat final des exécutions des hooks.
     */
    public function callHook($name, array $args = [])
    {
        return $this->container->callHook($name, $args);
    }

    /**
     * Ajoute les environnements à l'application (clé=>machine).
     *
     * @param array $env Liste des environnements.
     */
    public function setEnvironnement(array $env)
    {
        $this->environnement = $env;

        return $this;
    }

    /**
     * Retourne la requête courante.
     *
     * @return ServerRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Retourne la clé de l'environnement ou une chaine vide si la machine n'est pas reconnue.
     *
     * @return string
     */
    public function getEnvironment()
    {
        if (!empty($this->environnement)) {
            $host      = gethostname();
            $authority = $this->request->getUri()->getAuthority();

            foreach ($this->environnement as $key => $env) {
                if (in_array($host, $env) || in_array($authority, $env)) {
                    return $key;
                }
            }
        }

        return '';
    }

    /**
     * Si la machine fait partie de l'environnement passé en paramètre.
     *
     * @param string $nameEnv Nom de l'environnement.
     *
     * @return bool
     */
    public function isEnvironnement($nameEnv)
    {
        $authority = $this->request->getUri()->getAuthority();

        return isset($this->environnement[ $nameEnv ]) && (
            in_array(gethostname(), $this->environnement[ $nameEnv ]) ||
            in_array($authority, $this->environnement[ $nameEnv ])
            );
    }

    /**
     * Charge les instances des services hors modules.
     *
     * @return array
     */
    abstract protected function loadServices();

    /**
     * Charge les instances des contrôleurs dans la table des modules (clé => objet).
     *
     * @return object[]
     */
    abstract protected function loadModules();

    /**
     * Cherche les routes des modules et les charge dans l'application.
     */
    protected function loadRoutesAndServices()
    {
        foreach ($this->modules as $module) {
            if ($module->getPathRoutes()) {
                $routesConfig = Util::getJson($module->getPathRoutes());
                $this->routes += $routesConfig;
            }

            if ($module->getPathServices()) {
                $servicesConfig = Util::getJson($module->getPathServices());
                $this->services += $servicesConfig;
            }
        }
    }

    /**
     * Si le parmètre est une réponse alors celle-ci est renvoyé,
     * sinon une réponse est créé à partir des données du paramètre.
     *
     * Les données doivent pouvoir être prise en charge par le Stream de la réponse.
     *
     * @param ResponseInterface|bool|float|int|ressource|string|null $response
     *
     * @return ResponseInterface
     */
    protected function parseResponse($response)
    {
        return !($response instanceof ResponseInterface)
            ? new Response(200, new Stream($response))
            : $response;
    }
}
