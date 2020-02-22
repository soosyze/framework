<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\ServerRequest;
use Soosyze\Components\Http\Stream;
use Soosyze\Components\Router\Router;
use Soosyze\Components\Util\Util;
use Soosyze\Container;

/**
 * Coeur de l'application, il est le ciment qui unis les modules et les services.
 * Il possède la logique de framework (le router, les configurations... ).
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
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
     * Le nom de l'environnement par défaut.
     *
     * @var string
     */
    protected $environnementDefault = '';

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
    private $settings = [];

    /**
     * Requête courante à la création de la classe.
     *
     * @var ServerRequestInterface
     */
    private $request;

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
    public static function getInstance(ServerRequestInterface $request = null)
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
     * Revoie la valeur d'un paramètre du framework ou le paramètre par défaut avec l'environnement en suffixe.
     *
     * @param string $key
     * @param string $default
     * @param bool   $addEnv
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getSettingEnv($key, $default = '', $addEnv = true)
    {
        $setting = $this->getSetting($key, $default);
        if (!\is_string($setting)) {
            throw new \InvalidArgumentException('The framework parameter must return a string.');
        }
        $env = $addEnv
            ? $this->getEnvironment()
            : '';

        return "$setting/$env";
    }

    /**
     * Initialise le routeur et le container. Charge les configurations, les routes,
     * les services et les modules. Transmet le container aux contrôleurs.
     *
     * @return $this
     */
    public function init()
    {
        $config = new Config($this->getDir('config'));

        $this->container = (new Container)
            ->setConfig($config)
            ->setInstance('core', $this)
            ->setInstance('config', $config);

        $this->services = $this->loadServices();
        $this->container->setServices($this->services);

        $this->loadRoutesAndServices();

        $this->router = (new Router($this->container))
            ->setRequest($this->request)
            ->setConfig($config)
            ->setBasePath($this->request->getBasePath());

        $this->container->addServices($this->services)
            ->setInstance('router', $this->router);

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

        if (($route = $this->router->parse($request)) && $response->getStatusCode() === 404) {
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
     * Ajoute une fonction pour qu'elle puisse être utilisée par le container.
     *
     * @param string   $name Clé pour appeler la fonction.
     * @param callable $func Fonction à exécuter.
     *
     * @return $this
     */
    public function addHook($name, callable $func)
    {
        return $this->container->addHook($name, $func);
    }

    /**
     * Ajoute les environnements à l'application (clé=>machine).
     *
     * @param array $env Liste des environnements.
     *
     * @return $this
     */
    public function setEnvironnement(array $env)
    {
        $this->environnement = $env;

        return $this;
    }

    /**
     * Ajoute l'environnement par défaut.
     *
     * @param string $nameEnv
     *
     * @return $this
     */
    public function setEnvironmentDefault($nameEnv)
    {
        $this->environnementDefault = $nameEnv;

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

        return $this->environnementDefault;
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
        ) || $this->environnementDefault === $nameEnv;
    }

    /**
     * Retourne le répertoire d'une ressource en fonction
     * du chemin de base du serveur, d'un paramètre des configurations et de l'environnement.
     *
     * @param string $key     Paramètres du framework.
     * @param string $default Valeur par défaut.
     * @param bool   $addEnv  Si le retour doit prendre en compte l'environnement.
     *
     * @throws \InvalidArgumentException The framework parameter must return a string.
     * @return string
     */
    public function getDir($key, $default = '', $addEnv = true)
    {
        $root = $this->getSetting('root', '');
        $dir  = $this->getSettingEnv($key, $default, $addEnv);

        return Util::cleanDir("$root/$dir");
    }

    /**
     * Retourne le répertoire d'une ressource en fonction
     * du chemin de base de la requête, d'un paramètre des configurations et de l'environnement.
     *
     * @param string $key     Paramètres du framework.
     * @param string $default Valeur par défaut.
     * @param bool   $addEnv  Si le retour doit prendre en compte l'environnement.
     *
     * @throws \InvalidArgumentException The framework parameter must return a string.
     * @return string
     */
    public function getPath($key, $default = '', $addEnv = true)
    {
        $root = $this->request->getBasePath();
        $dir  = $this->getSettingEnv($key, $default, $addEnv);

        return $root . Util::cleanPath("$dir");
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
     *
     * @return void
     */
    protected function loadRoutesAndServices()
    {
        $modules = $this->loadModules();
        foreach ($modules as $module) {
            if ($module->getPathRoutes()) {
                include_once $module->getPathRoutes();
            }

            if ($module->getPathServices()) {
                $this->services += Util::getJson($module->getPathServices());
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
