<?php

/**
 * Soosyze Framework http://soosyze.com
 * 
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Soosyze\Components\Http\Reponse,
    Soosyze\Components\Http\ServerRequest,
    Soosyze\Components\Http\Stream;

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
     * Ajoute le container d'injection de dépendance au contrôleur.
     *
     * @param Container $container CID.
     */
    public function setContainer( $container )
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
    public function get( $key )
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
     * Appel un service directement par son nom.
     * 
     * @codeCoverageIgnore Fonction testé directement avec l'objet Container.
     *
     * @param string $name Nom du service.
     * @param array $arg Paramètres passés à la fonction.
     * 
     * @return object
     */
    public function __call( $name, $arg )
    {
        return $this->get($name);
    }

    /**
     * Retourne une réponse avec le statut 404.
     * 
     * @param string $text
     * 
     * @return Reponse
     */
    protected function get404( $text = 'Page Not Found, Sorry, but the page you were trying to view does not exist.' )
    {
        return new Reponse(404, new Stream($text));
    }
}