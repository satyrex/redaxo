<?php

/**
 * URL-Rewrite Addon
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @package redaxo\url-rewrite
 */

/**
 * URL Simple Rewrite Anleitung:
 *
 *   .htaccess file in das root verzeichnis:
 *     RewriteEngine Off
 */
class rex_url_rewriter_simple extends rex_url_rewriter
{

    // Parameter aus der URL für das Script verarbeiten
    public function prepare()
    {
        global $article_id, $clang;

        if (preg_match('@^/([0-9]*)-([0-9]*)@', $_SERVER['QUERY_STRING'], $_match)) {
            $article_id = $_match[1];
            $clang = $_match[2];
        } elseif ((empty( $_GET['article_id'])) && ( empty( $_POST['article_id']))) {
            $article_id = rex::getProperty('start_article_id');
        }
    }

    // Url neu schreiben
    public function rewrite(rex_extension_point $ep)
    {
        // Url wurde von einer anderen Extension bereits gesetzt
        if ($ep->getSubject() != '') {
            return;
        }
        $params = $ep->getParams();
        return '?/' . $params['id'] . '-' . $params['clang'] . '-' . $params['name'] . '.htm' . $params['params'];
    }
}
