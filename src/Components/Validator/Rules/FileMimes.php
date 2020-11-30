<?php

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
class FileMimes extends FileExtensions
{
    /**
     * Liste des extensions prises en charge.
     *
     * @var array
     */
    protected $mimetypes = [];

    /**
     * Test si l'extension du fichier est autorisée.
     *
     * @param string $key   Clé du test.
     * @param mixed  $value Valeur à tester.
     * @param string $arg   Liste des extensions autorisées.
     * @param bool   $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        parent::test('file_extensions', $value, $arg, $not);

        if ($this->hasErrors()) {
            return;
        }

        $this->mimetypes = include 'mimetypes_by_extensions.php';
        $info            = $this->getMime($value);

        if ($not) {
            $extension = $this->getExtension($value);
            $this->validMime($info, $extension);
        } else {
            $this->validNotMime($info, $arg);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output                  = parent::messages();
        $output[ 'mimes' ]       = 'L\extension ne correspond pas au mimetype de :label !';
        $output[ 'not_mimes' ]   = 'L\extension ne doit pas correspondre au mimetype de :label !';
        $output[ 'error_mimes' ] = 'L\'extension :ext du fichier :label n\'est pas pris en charge !';

        return $output;
    }

    /**
     * Test si l'extension du fichier est autorisée.
     *
     * @param string $info      Information sur le mimetype du fichier.
     * @param sting  $extension L'extension attendu.
     *
     * @return void
     */
    protected function validMime($info, $extension)
    {
        if (($mime = $this->getMimeByExtension($extension)) === false) {
            return;
        }

        if (is_array($mime) && !in_array($info, $mime)) {
            $this->addReturn('file_mimes', 'mimes');
        } elseif (!is_array($mime) && $mime !== $info) {
            $this->addReturn('file_mimes', 'mimes');
        }
    }

    /**
     * Test si l'extension du fichier ne correspond pas aux extensions autorisées.
     *
     * @param string $info       Information sur le mimetype du fichier.
     * @param string $extensions Liste d'extensions séparées par une virgule.
     *
     * @return void
     */
    protected function validNotMime($info, $extensions)
    {
        if (($mimes = $this->getMimesByExtensions(explode(',', $extensions))) === false) {
            return;
        }

        if (in_array($info, $mimes)) {
            $this->addReturn('file_mimes', 'not_mimes');
        }
    }

    /**
     * Récupère des mimestypes à partir d'une liste d'extensions.
     *
     * @param string[] $extensions Liste d'extensions.
     *
     * @return array|bool Retourne un tableau de mimestype ou 1 en cas d'erreur.
     */
    protected function getMimesByExtensions(array $extensions)
    {
        $output = [];
        foreach ($extensions as $ext) {
            if (($mime = $this->getMimeByExtension($ext)) === false) {
                return false;
            }
            if (is_array($mime)) {
                $output = array_merge($output, $mime);
            } else {
                $output[] = $mime;
            }
        }

        return $output;
    }

    /**
     * Retourne là ou les mimetypes à partir d'une extension
     * ou FALSE si aucuns mimetypes n'est trouvés.
     *
     * @param string $extension Nom de l'extension.
     *
     * @return bool|string|array
     */
    protected function getMimeByExtension($extension)
    {
        if (!isset($this->mimetypes[ $extension ])) {
            $this->addReturn('file_mimes', 'error_mimes', [ ':ext' => $extension ]);

            return false;
        }

        return $this->mimetypes[ $extension ];
    }
}
