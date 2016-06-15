<?php
$timeStart = microtime(true);
$currentTime = 0;
$urls = file('url.txt'); // считываем урлы из файла в массив
shuffle($urls); // мешаем массив с урлами случайным образом
$file_urls = fopen('url.txt', 'a');
ftruncate($file_urls,0); // очищаем файл с урлами
fclose($file_urls);
$urlsCount = count($urls);
$proxy = file('proxy.txt');
$proxyCount = count($proxy);

foreach ($urls as $num => $url){
    curlPing($url);
    $currentTime = microtime(true) - $timeStart;
    // выводим инфу в формате текущий_урл/всего_урлов || скорость урл/мин
    echo 'Pinged ' . ($num + 1) . '/' . $urlsCount . ' || Speed: ' . round($num * 60 / $currentTime) . ' urls/min', PHP_EOL;
}

function curlPing($url)
{
    global $proxyCount;
    global $proxy;
    $rand = rand(0,$proxyCount - 1); 
    $proxyRandom = trim($proxy[$rand]); // устанавливаем случайную прокси
    $ch = curl_init ();
    curl_setopt ($ch, CURLOPT_URL, 'http://www.google.com/webmasters/sitemaps/ping?sitemap=' . trim($url));
    curl_setopt ($ch, CURLOPT_PROXY, $proxyRandom);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt ($ch, CURLOPT_FAILONERROR, true);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close ($ch);
    // если ошибка, то рекурсивно запускаем функцию
    if (!$result) {
        curlPing($url);
    }
    return $result;
}

echo 'Pinged ' . $urlsCount . ' urls / ' . round($currentTime) . ' sec or ' . round($currentTime / 60, 1) . ' min';