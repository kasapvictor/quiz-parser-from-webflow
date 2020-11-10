<?php
require_once "vendor/autoload.php";
require_once "functions.php";

use \Wa72\HtmlPrettymin\PrettyMin;
use \Wa72\HtmlPageDom\HtmlPage;

//=============================================
//$url = "https://lessons.webflow.io/quiz-2";
$url = $_POST['source'];

$source = new HtmlPage(file_get_contents($url));

$source->indent()->save();

/*
 * quiz html
 */
$quiz = $source->filter(".wrap-quiz");

/*
 * save quiz html
 */
$quiz = html_entity_decode($quiz, ENT_NOQUOTES, "UTF-8");
file_put_contents(__DIR__."/done/html/quiz.html", $quiz);

/*
 * get link stylesheet
 */
$style_url = $source->filter("link[rel=stylesheet]")->attr("href");

/*
 * save original styles
 */
get_curl_gzip($style_url, __DIR__."/done/styles/quiz.css");

/*
 * clean original styles and create a new style
 */
// get new html of quiz
$new_quiz = new HtmlPage(file_get_contents( __DIR__."/done/html/quiz.html"));
$quiz_classes = [];
// foreach all elements and insert the array class
$new_quiz->filter("[class]")->each(
    function ($el, $i) use (&$quiz_classes) {
        $el_class = $el->filter('[class]')->attr('class');
        $whitespace = strripos($el_class, " ");

        // if element has more then one class then it will be explode in new array
        // then this new array go each and each element insert into quiz_class array
        if ($whitespace) {
            $classes = explode(" ", $el_class);
            foreach ( $classes as $class ) {
                $quiz_classes[] = $class;
            }
        } else {
            $quiz_classes[] = $el_class;
        }
    }
);

$quiz_classes = array_unique($quiz_classes);

// find all class of style.css and save these into new style file
$styles = file_get_contents( __DIR__."/done/styles/quiz.css");

// get basic styles
$re = '/^@font.*/mi';
preg_match($re, $styles, $matches, PREG_OFFSET_CAPTURE, 0);
$basic_styles = $matches[0][0];

// delete all rows and comments before 'Start of custom Webflow CSS'
$re = '/^.*\*\//mis';
$styles = preg_replace($re,"", $styles);

// create media_queries_array from styles
$re = '/@.*/mis';
preg_match($re, $styles, $matches, PREG_OFFSET_CAPTURE, 0);
$media_queries_array = $matches[0][0];
$media_queries_array = explode("}\n}\n", $media_queries_array);

// delete all @media queries from css and create new media_array
$styles = preg_replace($re,"", $styles);

$styles = explode("}\n", $styles);

$quiz_style = [];

// create regular styles
foreach ( $quiz_classes as $class ) {
    foreach ($styles as $style) {
        $match = strpos($style, ".".$class);
        if ($match !== false)  $quiz_style[] = "\n" . $style . "}";
    }
}


// filtering media_queries_array
$media_queries_styles = [];

foreach ( $media_queries_array as $query ) {
    $media = '/^@.*\)/mi';
    preg_match($media, $query, $matches_media, PREG_OFFSET_CAPTURE, 0);

    $style = '/\.\w.*?\{.*?\}/si';
    preg_match_all($style, $query, $matches_styles, PREG_OFFSET_CAPTURE, 0);

    $query_styles = "";

    foreach ($matches_styles[0]  as $style ) {
        foreach ( $quiz_classes as $class ) {
            $match = strpos($style[0], ".".$class);
            if ($match !== false) $query_styles .= "\n" . $style[0] . "\n";
        }
    }
    $media_queries_styles[$matches_media[0][0]] = $query_styles;
}

// add basic styles to top quiz styles
$quiz_style = $basic_styles . "\n" . implode("", $quiz_style);

// add media queries styles
$quiz_style .= "\n \n /* \n \n  media queries \n \n */ \n \n";
foreach ( $media_queries_styles as $k => $v ) {
    $quiz_style .= $k . "{" . $v . "} \n \n";
}

// change and downloads src from background-images
$re = '/https[^;,]+(jpg|jpeg|png|gif|svg)/mi';
preg_match_all($re, $quiz_style, $matches, PREG_OFFSET_CAPTURE, 0);
$images = [];

// create array urls and names of images
foreach($matches[0] as $match) {
    $re = '/https:\/\/.*\/(.*)/mi';
    preg_match_all($re, $match[0], $image_names, PREG_SET_ORDER, 0);

    foreach ($image_names as $image_name) {
        $images[$image_name[0]] = $image_name[1];
    }
}

// change names and paths of styles
foreach ($images  as $url => $name ) {
    file_put_contents("done/images/" . $name, fopen($url, 'r'));
    $quiz_style = str_replace($url, "../images/" . $name, $quiz_style);
}

$extra_classes = "
    textarea {resize: none;}
";

$quiz_style .= $extra_classes;

// save new quiz styles
file_put_contents(__DIR__."/done/styles/quiz.css", $quiz_style);

/*
 * create and save javascript file
 */
file_put_contents(__DIR__."/done/scripts/quiz.js", '');








