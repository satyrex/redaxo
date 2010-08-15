<?php

/**
 * Mediapool Addon
 *
 * @author jan[dot]kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @package redaxo5
 * @version svn:$Id$
 */

$mypage = 'mediapool';

$REX['ADDON']['name'][$mypage] = $I18N->msg('mediapool');
$REX['ADDON']['perm'][$mypage] = ''; // hasStructurePerm
$REX['ADDON']['version'][$mypage] = "0.1";
$REX['ADDON']['author'][$mypage] = "Jan Kristinus";
$REX['ADDON']['supportpage'][$mypage] = '';
$REX['ADDON']['navigation'][$mypage] = array('block'=>'system');

if ($REX['REDAXO'])
{
  $I18N->appendFile($REX['SRC_PATH'] . '/addons/' . $mypage . '/lang');
  include_once $REX['SRC_PATH'] . '/addons/' . $mypage . '/functions/function_rex_mediapool.inc.php';
  $loader->addDirectory($REX['SRC_PATH'] . '/addons/' . $mypage . '/classes/');
}