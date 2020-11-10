<?php
require_once "vendor/autoload.php";
require_once "functions.php";

use \Wa72\HtmlPrettymin\PrettyMin;
use \Wa72\HtmlPageDom\HtmlPage;

//=============================================
$url = "https://lessons.webflow.io/quiz-2";

$source = new HtmlPage(file_get_contents($url));

$source->indent()->save();

/*
 * получаем верстку квиза из всего html документа
 */
$quiz = $source->filter(".wrap-quiz");

/*
 * сохраняем верстку
 */
$quiz = html_entity_decode($quiz, ENT_NOQUOTES, "UTF-8");
file_put_contents(__DIR__."/parse2/quiz.html", $quiz);

/*
 * получаем ссылку на файл стилей
 */
$style_url = $source->filter("link[rel=stylesheet]")->attr("href");

/*
 * сохраняем весь файл стилей
 */
get_curl_gzip($style_url, __DIR__."/parse2/original.css");

/*
 * создаем новый файл стилей для квиза
 * удаляем все лишнее
 * скачиваем изображения и перезаписываем пути для них
 */

// получаем верстку квиза
$quiz_html = new HtmlPage(file_get_contents( __DIR__."/parse2/quiz.html"));

// массив для классов в верстке квиза
$quiz_classes = [];

// перебор каждого элемента с атрибутом class="...
$quiz_html->filter("[class]")->each(
    function ($el, $i) use (&$quiz_classes) {
        $el_class = $el->filter('[class]')->attr('class');
        $whitespace = strripos($el_class, " ");

        // если у элемента больше одного класса,
        // то разбить строку на массив и добавить отдельно каждый элемент в массив классов
        if ($whitespace) {
            $classes = explode(" ", $el_class);
            foreach ( $classes as $class ) {
                $quiz_classes[] = "." . $class;
            }
        } else {
            $quiz_classes[] = "." . $el_class;
        }
    }
);

// удялем дубли классов
$quiz_classes = array_unique($quiz_classes);

// получаем все стили в переменную $styles
$styles = file_get_contents( __DIR__."/parse2/original.css");

// удаляем комментарии без "="
$re = '/\/\*.*\*\//m';
$styles = preg_replace($re, "", $styles);

// удаляем комментарии c "="
$re = '/\/\*\s?=+\s?.+\s?.+\*\//m';
$styles = preg_replace($re, "", $styles);

// удаляем все стили reset, начинается с html
$re = '/html.*/m';
$styles = preg_replace($re, "", $styles);

//  все медиа запросы из основного массива стилей добавляем в новый массив $media_queries
$media_queries = [];
$re = '/@media([^{]+)\{([\s\S]+?})\s*}/m';
preg_match_all($re, $styles, $matches, PREG_SET_ORDER, 0);
foreach ($matches as $match) $media_queries[] = $match[0];

// удаляем все медиа запросы из основного файла стилей
$styles = preg_replace($re, "", $styles);

// разбиваем стили на массив
$styles = explode("}", $styles);


// перебираем все классы из верстки
// если есть совпадения с массивом стилей то сохраняем в новый массив $quiz_style
$quiz_style = [];
foreach ( $quiz_classes as $class ) {
    foreach ($styles as $style) {
        $match = strpos($style, $class);
        if ($match !== false)  $quiz_style[] = "\n" . $style . "}";
    }
}

// присваиваем переменной $style данные массива $quiz_style
$styles = $quiz_style;

// фильтруем медиазапросы
// формируем новый массив где ключ это медиа запрос
// значение это стили для этого запроса
// оставляем только нужные стили
$media_queries_styles = [];
foreach ( $media_queries as $query ) {
    $media = '/^@.*\)/mi';
    preg_match($media, $query, $matches_media, PREG_OFFSET_CAPTURE, 0);

    $style = '/\.\w.*?\{.*?\}/si';
    preg_match_all($style, $query, $matches_styles, PREG_OFFSET_CAPTURE, 0);

    $query_styles = "";

    foreach ($matches_styles[0]  as $style ) {
        foreach ( $quiz_classes as $class ) {
            $match = strpos($style[0], $class);
            if ($match !== false) $query_styles .= "\n" . $style[0] . "\n";
        }
    }
    $media_queries_styles[$matches_media[0][0]] = $query_styles;
}

// склеиваем массив стилей  в строку
$styles = implode("", $styles);

// добавление медиазапросов к основному файлу стилей
$styles .= "\n \n /* \n \n  media queries \n \n */ \n \n";
foreach ( $media_queries_styles as $k => $v ) {
    $styles .= $k . " {" . $v . "} \n \n";
}

// загрузка изображений и изменение адреса в стилях
$re = '/https[^;,]+(jpg|jpeg|png|gif|svg)/mi';
preg_match_all($re, $styles, $matches, PREG_OFFSET_CAPTURE, 0);
$images = [];

// создание массива ссылок на изображений
// ссылка - ключ
// имя - значение
foreach($matches[0] as $match) {
    $re = '/https:\/\/.*\/(.*)/mi';
    preg_match_all($re, $match[0], $image_names, PREG_SET_ORDER, 0);

    foreach ($image_names as $image_name) {
        $images[$image_name[0]] = $image_name[1];
    }
}

// обновление адресов картинок в файле стилей
foreach ($images  as $url => $name ) {
    file_put_contents("parse2/" . $name, fopen($url, 'r'));
    $styles = str_replace($url, "./" . $name, $styles);
}

$extra_classes = "
    textarea {resize: none;}
";

$styles .= $extra_classes;

// сохранение файла стиле
file_put_contents(__DIR__."/parse2/quiz.css", $styles);




