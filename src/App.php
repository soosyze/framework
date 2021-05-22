<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\Http\Response;
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
     * Conteneur d'injection de dépendance (CID).
     *
     * @var Container
     */
    protected $container;

    /**
     * Instance unique de App.
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Le routeur.
     *
     * @var Router
     */
    private $router;

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
    final private function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Singleton pour une classe abstraite.
     *
     * @param ServerRequestInterface $request Requête courante de l'application.
     *
     * @return self Instance unique de App.
     */
    public static function getInstance(ServerRequestInterface $request): self
    {
        if (self::$instance === null) {
            self::$instance = new static($request);
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
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Revoie les paramètres du framework.
     *
     * @return array
     */
    public function getSettings(): array
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
    public function getSetting(string $key, $default = '')
    {
        return $this->settings[ $key ] ?? $default;
    }

    /**
     * Revoie la valeur d'un paramètre du framework ou le paramètre par défaut avec l'environnement en suffixe.
     *
     * @param string $key
     * @param string $default
     * @param bool   $addEnv
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getSettingEnv(
        string $key,
        string $default = '',
        bool $addEnv = true
    ): string {
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
    public function init(): self
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
    public function run(): ResponseInterface
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
    public function get(string $key): object
    {
        return $this->container->get($key);
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
    public function callHook(string $name, array $args = [])
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
    public function addHook(string $name, callable $func): self
    {
        $this->container->addHook($name, $func);

        return $this;
    }

    /**
     * Ajoute les environnements à l'application (clé=>machine).
     *
     * @param array $env Liste des environnements.
     *
     * @return $this
     */
    public function setEnvironnement(array $env): self
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
    public function setEnvironmentDefault(string $nameEnv): self
    {
        $this->environnementDefault = $nameEnv;

        return $this;
    }

    /**
     * Retourne la requête courante.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Retourne la clé de l'environnement ou une chaine vide si la machine n'est pas reconnue.
     *
     * @return string
     */
    public function getEnvironment(): string
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
    public function isEnvironnement(string $nameEnv): bool
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
     *
     * @return string
     */
    public function getDir(
        string $key,
        string $default = '',
        bool $addEnv = true
    ): string {
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
     *
     * @return string
     */
    public function getPath(
        string $key,
        string $default = '',
        bool $addEnv = true
    ): string {
        $root = $this->request->getBasePath();
        $dir  = $this->getSettingEnv($key, $default, $addEnv);

        return $root . Util::cleanPath($dir);
    }

    /**
     * Charge les instances des services hors modules.
     *
     * @return array
     */
    abstract protected function loadServices(): array;

    /**
     * Charge les instances des contrôleurs dans la table des modules (clé => objet).
     *
     * @return Controller[]
     */
    abstract protected function loadModules(): array;

    /**
     * Cherche les routes des modules et les charge dans l'application.
     *
     * @return void
     */
    protected function loadRoutesAndServices(): void
    {
        $modules = $this->loadModules();
        foreach ($modules as $module) {
            if ($module->getPathBoot()) {
                include_once $module->getPathBoot();
            }
            if ($module->getPathRoutes()) {
                include_once $module->getPathRoutes();
            }

            if ($module->getPathServices()) {
                $services = include_once $module->getPathServices();
                if ($services !== true) {
                    $this->services += $services;
                }
            }
        }
    }

    /**
     * Si le parmètre est une réponse alors celle-ci est renvoyé,
     * sinon une réponse est créé à partir des données du paramètre.
     *
     * Les données doivent pouvoir être prise en charge par le Stream de la réponse.
     *
     * @param ResponseInterface|bool|float|int|object|ressource|string|null $response
     *
     * @return ResponseInterface
     */
    protected function parseResponse($response): ResponseInterface
    {
        return !($response instanceof ResponseInterface)
            ? new Response(200, new Stream($response))
            : $response;
    }
}
