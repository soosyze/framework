<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Soosyze\Components\Http\Response;
use Soosyze\Components\Http\Stream;
use Psr\Http\Message\RequestInterface;

/**
 * Les méthodes du contrôleur sont appelées à partir d'une requête http
 * puis retourne une réponse.
 *
 * Le contrôleur est un élément essentiel du schéma MVC,
 * il fait le lien entre votre modèle de données et vos vues.
 * Il est l'élément de base de vos modules.
 *
 * @author Mathieu NOËL
 */
class Controller
{
    /**
     * Chemin du fichier contenant les routes.
     *
     * @var string
     */
    protected $pathRoutes = '';

    /**
     * Chemin du fichier contenant les services.
     *
     * @var string
     */
    protected $pathServices = '';

    /**
     * Container d'injection de dépendance (CID).
     *
     * @var Container
     */
    protected $container;

    /**
     * Appel un service directement par son nom.
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $name Nom du service.
     * @param array $arg Paramètres passés à la fonction.
     *
     * @return object
     */
    public function __call($name, $arg)
    {
        return $this->get($name);
    }

    /**
     * Ajoute le container d'injection de dépendance au contrôleur.
     *
     * @param Container $container CID.
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Appel le container.
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $key Nom du service.
     *
     * @return object Service dans le container.
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * Retourne le chemin du fichier de configuration des routes.
     *
     * @return string
     */
    public function getPathRoutes()
    {
        return $this->pathRoutes;
    }

    /**
     * Retourne le chemin du fichier de configuration des services.
     *
     * @return string
     */
    public function getPathServices()
    {
        return $this->pathServices;
    }

    /**
     * Retourne une réponse avec le statut 404.
     *
     * @codeCoverageIgnore Fonction testé directement avec l'objet Response.
     *
     * @param string $stream
     * @param RequestInterface $request
     *
     * @return Response
     */
    protected function get404($stream = null, RequestInterface $request = null)
    {
        $text          = 'Page %s Not Found. Sorry, but the page you were trying to view does not exist.';
        $stream_output = $stream instanceof RequestInterface && $request === null
            ? sprintf($text, $stream->getUri()->getQuery())
            : sprintf($text, $request->getUri()->getQuery());

        return new Response(404, new Stream($stream_output));
    }
}
