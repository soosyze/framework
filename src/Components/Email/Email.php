<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Form
 * @author Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Email;

/**
 * Créer un email de type text ou HTML.
 *
 * @author Mathieu NOËL
 */
class Email
{
    /**
     * Le sujet du email.
     *
     * @var string
     */
    protected $subject;

    /**
     * Le message texte ou html.
     *
     * @var string
     */
    protected $message;

    /**
     * Les paramètres d'entête.
     *
     * Pré-remplis par défaut pour un email basique.
     *
     * @var array
     */
    protected $headers = [
        'mime-version' => [ '1.0' ],
        /* Priorité de 1(du plus important) à 5 */
        'x-priority'   => [ '3' ],
        /* Type de données attendue par le webmail */
        'content-type' => [ 'text/plain; charset="iso-8859-1"' ]
    ];

    /**
     * Ajoute un destinataire.
     *
     * @param string $email Email du destinataire.
     * @param string $name Nom du destinataire.
     *
     * @return $this
     */
    public function to($email, $name = '')
    {
        $value = $this->parseMail($this->filtreEmail($email), $this->filtreName($name));

        return $this->withAddedHeader('to', $value);
    }

    /**
     * Ajoute un ou plusieurs déstinataires en copie du email.
     *
     * @param string $email Email en copie.
     * @param string $name Nom du destinataire.
     *
     * @return $this
     */
    public function addCc($email, $name = '')
    {
        $value = $this->parseMail($this->filtreEmail($email), $this->filtreName($name));

        return $this->withAddedHeader('cc', $value);
    }

    /**
     * Ajoute un ou plusieurs destinataires en copie cachée du email.
     *
     * @param type $email Email en copie cachée.
     * @param string $name Nom du destinataire.
     *
     * @return $this
     */
    public function addBcc($email, $name = '')
    {
        $value = $this->parseMail($this->filtreEmail($email), $this->filtreName($name));

        return $this->withAddedHeader('bcc', $value);
    }

    /**
     * Ajoute une adresse de provenance.
     *
     * @param string $email Email de provenance.
     * @param string $name Nom du destinataire.
     *
     * @return $this
     */
    public function from($email, $name = '')
    {
        $value = $this->parseMail($this->filtreEmail($email), $this->filtreName($name));

        return $this->withHeader('from', $value);
    }

    /**
     * Ajoute une adresse de retour.
     *
     * @param string $email Email de retour.
     * @param string $name Nom du destinataire.
     *
     * @return $this
     */
    public function replayTo($email, $name = '')
    {
        $value = $this->parseMail($this->filtreEmail($email), $this->filtreName($name));

        return $this->withHeader('replay-to', $value);
    }

    /**
     * Ajoute un sujet au email, le texte est encodé au format ASCII.
     *
     * @param string $subj Sujet du email.
     *
     * @return $this
     */
    public function subject($subj)
    {
        $this->subject = mb_convert_encoding($subj, "ASCII");

        return $this;
    }

    /**
     * Ajoute un message, le texte est encodé au format ASCII.
     *
     * @param string $msg Corp du email.
     *
     * @return $this
     */
    public function message($msg)
    {
        $this->message = mb_convert_encoding($msg, "ASCII");

        return $this;
    }

    /**
     * Déclare que le contenu du email est de l'HTML.
     *
     * @param $bool Si vrais le contenu sera envoyé en mode HTML.
     *
     * @return $this
     */
    public function isHtml($bool = true)
    {
        $key = 'content-type';

        return $bool
            ? $this->withHeader($key, 'text/html; charset="iso-8859-1"')
            : $this->withHeader($key, 'text/plain; charset="iso-8859-1"');
    }

    /**
     * Envoie l'email.
     *
     * @return bool Si l'email est bien envoyé.
     */
    public function send()
    {
        $to      = $this->getHeaderLine('to');
        $subject = $this->subject;
        $message = $this->message;

        return mail($to, $subject, $message, $this->parseHeaders());
    }

    /**
     * Renvoie le tableau d'en-tête.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Récupère une chaîne de valeurs séparées par des virgules pour un seul en-tête.
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string Si l'en-tête est trouvé alors il est renvoyé
     * toutes les valeurs de l'en-tête concaténés par une virgule, sinon une chaine vide.
     */
    public function getHeaderLine($name)
    {
        return $this->hasHeader($name)
            ? implode(',', $this->headers[ strtolower($name) ])
            : '';
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string[] Si l'en-tête est trouvé alors il est renvoyé
     * toutes ses valeurs, sinon un tableau vide.
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name)
            ? $this->headers[ strtolower($name) ]
            : [];
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headers[ strtolower($name) ]);
    }

    /**
     * Parse les données de l'entête pour l'envoi du email.
     *
     * @return string
     */
    public function parseHeaders()
    {
        $headers = '';
        foreach ($this->headers as $key => $value) {
            if ($key !== 'to') {
                $headers .= $key . ': ' . $this->getHeaderLine($key) . "\r\n";
            }
        }

        return $headers;
    }

    /**
     * Formalise les données d'un email est de son destinataire.
     *
     * @param string $email Email (from, bcc, cc, replayTo...).
     * @param string $name Nom du destinataire.
     *
     * @return string
     */
    protected function parseMail($email, $name = '')
    {
        return $output = $name !== ''
            ? '"' . $name . '" <' . $email . '>'
            : $email;
    }

    /**
     * Renvoyer une instance avec la valeur fournie en remplaçant l'en-tête spécifié.
     *
     * @param string $name Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return $this
     */
    protected function withHeader($name, $value)
    {
        $this->headers[ strtolower($name) ] = is_array($value)
            ? $value
            : [ $value ];

        return $this;
    }

    /**
     * Renvoyer une instance avec la valeur fournie en ajoutant l'en-tête spécifié.
     *
     * @param string $name Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return $this
     */
    protected function withAddedHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [ $value ];
        }
        /* Pour ne pas écraser les valeurs avec le array merge utilise une boucle simple */
        foreach ($value as $head) {
            $this->headers[ strtolower($name) ][] = $head;
        }

        return $this;
    }

    /**
     * Déclanche une exception si la valeur n'est pas un email.
     *
     * @param string $strEmail Email à filtrer.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException Le paramètre n'est pas une adresse email valide.
     */
    private function filtreEmail($strEmail)
    {
        $email = trim($strEmail);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(htmlspecialchars("$email is not a valid email."));
        }

        return $email;
    }

    /**
     * Déclenche une exception si la valeur n'est pas une chaine de caractère.
     * Sinon nettoie la chaine en supprimant les espaces en début et les retours à la ligne.
     *
     * @param string $name Nom d'un destinataire.
     *
     * @return string Chaine nettoyée.
     *
     * @throws \InvalidArgumentException Le paramètre n'est pas un nom de destinataire valide.
     */
    private function filtreName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(htmlspecialchars("$name is not a valid recipient."));
        }

        return trim(preg_replace('/[\r\n]+/', '', $name));
    }
}
