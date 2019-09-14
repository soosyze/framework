<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class Token extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test la validité d'un token ($_SESSION['token']) à une valeur de comparaison
     * et son rapport au temps ($_SESSION['token_time'])
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param int    $arg   Nombre de seconde ou le token est valide (défaut 15 minutes),
     *                      si la valeur du time = 0 alors le test du temps de validation n'est pas effectif.
     * @param bool   $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur time n'est pas numérique.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        if (session_id() == '') {
            @session_start([
                    'cookie_httponly' => true,
                    'cookie_secure'   => true
            ]);
        }

        if ($arg === false) {
            $arg = 900;
        }
        if (!is_numeric($arg)) {
            throw new \InvalidArgumentException('The time value must be numeric.');
        }
        
        $name = $this->getKeyValue();

        if (!isset($_SESSION[ 'token' ][ $name ]) && !isset($_SESSION[ 'token_time' ][ $name ])) {
            $this->addReturn($key, 'error');
        } elseif ($_SESSION[ 'token' ][ $name ] != $value) {
            $this->addReturn($key, 'invalid');
        } elseif ($_SESSION[ 'token_time' ][ $name ] <= (time() - intval($arg))) {
            $this->addReturn($key, 'time');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'error'   => 'Une erreur est survenue.',
            'invalid' => 'Le token n\'est pas valide.',
            'time'    => 'Vous avez attendu trop longtemps, veilliez recharger la page.'
        ];
    }
}
