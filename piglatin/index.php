<?php
/*
Plugin Name: Pig latgin
Plugin URI: http://wordpress.org/extend/plugins/piglatin/
Description: Overrides the current language and translates all messages into pig latin. This way you can easily spot, which messages were left untranslatable, while the interface is still usable. It's adapted from a Wordpress plugin.
Version: 0.9
Author: juanramon
Author URI: http://profiles.wordpress.org/nbachiyski/
*/

function word2pig($match) {
    $text = $match[0];
    $hyphen = '';
    $consonants = "bBcCdDfFgGhHjJkKlLmMnNpPqQrRsStTvVwWxXyYzZ";
    $vowels = "aAeEiIoOuU";

    $i = 0;
    if (false !== strpos($consonants, $text[0])) {
        $cons = $text[0];
        $i = 1;
        while ($i < strlen($text) && false !== strpos($consonants, $text[$i])) {
            $cons .= $text[$i];
            ++$i;
        }
        return substr($text, $i).$hyphen.$cons.'ay';
    }
    else if (false !== strpos($vowels, $text[0])) {
        return $text.'ay';
    }
    else {
        return $text;
    }
}

/**
 * Source: 
 */
function translation2pig($string) {
    if (strlen($string) < 3) {
        return $string;
    }
    /*
        do not translate tag names and attributes,
        entities, and %xxx encoded strings
    */
    $delimiters = array(
        '<.*?>',
        '\&#\d+;',
        '\&[a-z]+;',
        '%\d+\$[sd]',
        '%[sd]',
        '\s+',
    );
    $parts = preg_split('/('.implode('|', $delimiters).')/i', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $cnt = count($parts);
    for ($i = 0; $i < $cnt; ++$i) {
        $isdelim = false;
        foreach ($delimiters as $delim) {
            if (preg_match("/^$delim$/", $parts[$i])) {
                $isdelim = true;
                break;
            }
        }
        if ($isdelim) {
            continue;
        }
        $parts[$i] = preg_replace_callback('/[a-z]+/i', 'word2pig', $parts[$i]);
    }
    return implode('', $parts);
}

osc_add_filter('gettext', 'translation2pig');
osc_add_filter('ngettext', 'translation2pig');