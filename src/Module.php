<?php

namespace Soosyze;

interface Module
{
    /**
     * Retourne le chemin absolut du module
     */
    public function getModuleDir(): string;

    /**
     * Retourne le chemin du fichier de configuration des services.
     */
    public function getPathServices(): string;

    /**
     * Retourne le chemin du fichier de configuration des routes.
     */
    public function getPathRoutes(): string;
}
