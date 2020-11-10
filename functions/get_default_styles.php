<?php

function get_default_styles ($content)
{
    // удаляем все до дефолтных стилей webflow
    $re = '/(.*)@font/ms';
    $content = preg_replace($re, "@font", $content);

    // получаем дефолтные стили webflow
    $re = '/@font.*\/\*.*\*\//ms';
    preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
    $default_styles = $matches[0][0];

    // удаляем комментарии в дефолтных стилях
    $re = '/\/\*.*/ms';
    $default_styles = preg_replace($re, "", $default_styles);

    return $default_styles;
}