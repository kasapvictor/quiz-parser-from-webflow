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
 * получаем верстку квиза из всего html документа
 */
$quiz = $source->filter(".wrap-quiz");

/*
 * поиск атрибута data-quiz-file
 */
$file = $quiz->filter('[data-quiz-files]');

/*
 * поиск атрибута data-required
 * если атрибут установлен то добавить к input type="file" required
 */
$file_required = $file->attr('data-required');
$file_required = $file_required ? 'required' : '';

/*
 * если переменная не пустая то добавить верстку input type=file
 * если есть атрибут required то добавит чек бокс о том что файлов нет
 */
if ($file) {
    $html = "
        <div class='field-files quiz-files-wrapper'>
           <div class='file-wrapper'>
               <label>
                    <div class='wrap-preview-file'>+</div>
                    <input type='file' name='attachments[]' $file_required >
                </label>
                <span class='delete-file'>×</span>
            </div>
        </div>
       ";
    $file->setInnerHtml($html);
}

/*
 * поиск элменетов с атрибутом data-name
 * удалем все data-name
 * вместо этого ставим атрибут name со значением из data-name
 */
$data_names = $quiz->filter('[data-name]');
if ($data_names) {
    $data_names->each(function ($el, $i) {

        $parent = $el->closest('[data-quiz-group]');
        $name = $el->attr('data-name');
        $el->removeAttr('data-name');

        /*
         * если есть родительский узел с атрибутом data-quiz-group
         * то задать всем дочерним элементам значение родительского атрибута + []
         * что создает группировку элементов
         */
        if ($parent) {
            $el->setAttribute('value', $name);
            $el->setAttribute('name', $parent->attr('data-quiz-group') . "[]");
        } else {
            $el->setAttribute('name', $name);
        }
    });
}

/*
 * сохраняем верстку блока с квизом в файл html/quiz.html
 */
$quiz = html_entity_decode($quiz, ENT_NOQUOTES, "UTF-8");
file_put_contents(dirname(__DIR__)."/done/html/quiz.html", $quiz);

/*
 * вставляем квиз в тестовый блок test.html
 */
$test = new HtmlPage(file_get_contents(dirname(__DIR__)."/done/test.html"));
$test->filter(".wrap-test-quiz")->makeEmpty()->setInnerHtml("\n" . $quiz . "\n");
$test = html_entity_decode($test->indent()->save(), ENT_NOQUOTES, "UTF-8");

/*
 * сохраняем test.html c версткой квиза
 */
file_put_contents(dirname(__DIR__)."/done/test.html", $test);

/*
 * получаем ссылку на файл стилей
 */
$style_url = $source->filter("link[rel=stylesheet]")->attr("href");

/*
 * сохраняем весь файл стилей
 */
get_curl_gzip($style_url, dirname(__DIR__)."/done/styles/original.css");