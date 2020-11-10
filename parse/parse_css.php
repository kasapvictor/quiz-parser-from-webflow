<?php
require_once "vendor/autoload.php";
require_once "functions.php";

use \Wa72\HtmlPrettymin\PrettyMin;
use \Wa72\HtmlPageDom\HtmlPage;

/*
* создаем новый файл стилей для квиза
* удаляем все лишнее
* скачиваем изображения и перезаписываем пути для них
*/

// получаем верстку квиза
$quiz_html = new HtmlPage(file_get_contents( dirname(__DIR__)."/done/html/quiz.html"));

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

// удялем дубли классов квиза
$quiz_classes = array_unique($quiz_classes);

// присваиваем все стили в переменную $styles
$styles = file_get_contents( dirname(__DIR__)."/done/styles/original.css");

// получаем все медиазапросы ввиде массива
$media_queries = get_media_queries($styles);

// получаем дефолтные стили
$default_styles = get_default_styles($styles);

// удаляем все медиа запросы из стилей $styles
$re = '/@media([^{]+)\{([\s\S]+?})\s*}/m';
$styles = preg_replace($re, "", $styles);
// удаляем все медиа запросы из стилей $default_styles
$default_styles = preg_replace($re, "", $default_styles);
// удаляем все до кастомных стилей из стилей $styles
$re = '/.*Start of custom Webflow CSS.*\*\//ms';
$styles = preg_replace($re, "", $styles);

// разбиваем стили на массив
$styles = explode("}", $styles);
$default_styles = explode("}", $default_styles);

// перебираем все классы из верстки
// если есть совпадения с массивом стилей то сохраняем в новый массив $quiz_style
$quiz_style = [];
foreach ( $quiz_classes as $class ) {
    foreach ($default_styles as $style) {
        $match = strpos($style, $class);
        if ($match !== false)  $quiz_style[] = $style . "}";
    }
}

foreach ( $quiz_classes as $class ) {
    foreach ($styles as $style) {
        $match = strpos($style, $class);
        if ($match !== false)  $quiz_style[] = $style . "}";
    }
}

// присваиваем переменной $style данные массива $quiz_style
$styles = $quiz_style;

// склеиваем массив стилей  в строку
$styles = implode("", $styles);

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

// добавление медиазапросов
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
    file_put_contents("done/images/" . $name, fopen($url, 'r'));
    $styles = str_replace($url, "../images/" . $name, $styles);
}

$extra_classes = '
textarea {resize: none;}

.quiz-files-wrapper { 
    display: flex;
    flex-wrap: wrap;
    width: 100%; 
}

.quiz-files-wrapper input[type="file"] { display:none; }

.quiz-files-wrapper .file-wrapper {
    width: 100px;
    height: 100px;
    margin-right: 20px;
    margin-bottom: 40px;
    position: relative;
}

.quiz-files-wrapper .wrap-preview-file {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    border: 1px solid #e9e6e6;
    border-radius: 5px;
    cursor: pointer;
    font-size: 30px;
    font-weight: 100;
    color: #888;
    background: #f9f9f9;
    background-size: 90%;
    background-repeat: no-repeat;
    background-position: center;
    transition: all .2s;
}

.quiz-files-wrapper .wrap-preview-file svg {
    width: 90%;
}

.quiz-files-wrapper .wrap-preview-file span {
    max-width: 100px;
    padding: 5px;
    position: absolute;
    bottom: -30px;
    white-space: nowrap;    
    text-overflow: ellipsis;
    overflow: hidden;
    font-size: 12px;
}

.quiz-files-wrapper .wrap-preview-file:hover {  border: 1px solid #b6b4b4; }

.quiz-files-wrapper .delete-file {
    display:none;
}

.quiz-files-wrapper .has-file .delete-file {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 20px;
    height: 20px;
    position: absolute;
    top: -10px;
    right: -8px;
    font-size: 12px;
    color: #fff;
    font-weight: 400;
    cursor: pointer;
    transition: all .3s;
    background: #ff584b;
    border-radius: 50%;
    z-index: 100;
}

.warning {
    font-size: 12px;
    font-style: italic;
    font-weight: 100;
    color: coral;
}
';

$styles .= $extra_classes;

// сохранение файла стиле
file_put_contents(dirname(__DIR__)."/done/styles/quiz.css", $styles);
unlink(dirname(__DIR__)."/done/styles/original.css");






