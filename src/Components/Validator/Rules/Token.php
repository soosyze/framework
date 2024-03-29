<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Token extends Size
{
    /**
     * Test la validité d'un token ($_SESSION['token']) à une valeur de comparaison
     * et son rapport au temps ($_SESSION['token_time'])
     *
     * @param string $key   Clé du test.
     * @param string $value Valeur à tester.
     * @param mixed  $args  Nombre de seconde ou le token est valide (défaut 15 minutes),
     *                      si la valeur du time = 0 alors le test du temps de validation n'est pas effectif.
     * @param bool   $not   Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur time n'est pas numérique.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        if (session_id() === '') {
            @session_start([
                    'cookie_httponly' => true,
                    'cookie_secure'   => true
            ]);
        }

        $intervale = is_numeric($args)
            ? $this->getComparator($args)
            : 900;

        $name = $this->getKey();

        if (!isset($_SESSION[ 'token' ][ $name ]) && !isset($_SESSION[ 'token_time' ][ $name ])) {
            $this->addReturn($key, 'error');
        } elseif ($_SESSION[ 'token' ][ $name ] != $value) {
            $this->addReturn($key, 'invalid');
        } elseif ($_SESSION[ 'token_time' ][ $name ] <= (time() - (int) $intervale)) {
            $this->addReturn($key, 'time');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output              = parent::messages();
        $output[ 'error' ]   = 'An error has occurred.';
        $output[ 'invalid' ] = 'The token is not valid.';
        $output[ 'time' ]    = 'You have waited too long, please reload the page.';

        return $output;
    }
}
