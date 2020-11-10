<?php

function get_media_queries ($content)
{
    $media_queries = [];
    $re = '/@media([^{]+)\{([\s\S]+?})\s*}/m';
    preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $match) $media_queries[] = $match[0];
    return $media_queries;
}