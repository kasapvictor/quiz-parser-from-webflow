<?php
/*
* Сохраняет файлы из gzip (https) css, js указанную
*/
function get_curl_gzip(string $url = '', $save_to_path)
{
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$data = gzdecode(curl_exec($ch));
curl_close($ch);
file_put_contents($save_to_path, $data);
//return (string) $data;
}