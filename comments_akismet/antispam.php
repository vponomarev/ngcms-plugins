<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('naHAL!1');
}

// Load LIBRARY
loadPluginLibrary('comments', 'lib');
include_once root.'/plugins/comments_akismet/inc/Akismet.class.php';

class AntispamFilterComments extends FilterComments
{
    public function addComments($userRec, $newsRec, &$tvars, &$SQL)
    {
        $akis = new Akismet(home, pluginGetVariable('comments_akismet', 'akismet_apikey'));
        $akis->setAkismetServer(pluginGetVariable('comments_akismet', 'akismet_server'));
        if ($akis->isKeyValid()) {
            $akis->setCommentAuthor($SQL['author']);
            $akis->setCommentAuthorEmail($SQL['mail']);
            $akis->setCommentContent($SQL['text']);
            if ($akis->isCommentSpam()) {
                return ['result' => 0, 'errorText' => 'Akismet blocked your comment!'];
            } else {
                return 1;
            }
        } else {
            //print 'Akismet error';
            return ['result' => 0, 'errorText' => 'Akismet key is invalid! '.pluginGetVariable('comments_akismet', 'akismet_apikey')];
        }
    }
}

register_filter('comments', 'antispam', new AntispamFilterComments());
