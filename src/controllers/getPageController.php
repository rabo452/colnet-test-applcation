<?php 

include_once '../config.php';
include_once ROOT_DIR . '/db/MySqlDatabaseManager.php';
include_once ROOT_DIR . '/utils/getDomainFromUrl.php';
include_once ROOT_DIR . '/services/HtmlParserService.php';
include_once ROOT_DIR . '/services/RequestService.php';
include_once ROOT_DIR . '/models/GeneralStatistic.php';
include_once ROOT_DIR . '/services/StatisticService.php';

// as we don't use any framework, we should resort to the simplest request handlers in php
if ($_POST['page_url'] && $_POST['html_element']) {
    try {
        $url = $_POST['page_url'];
        $element = $_POST['html_element'];
        $domain = getDomainFromUrl($url);

        if (strpos($element, ">") !== false || strpos($element, "<")) {
            die(json_encode(array(
                "message" => "unable to use the html element with '<' or '>' symbols"
            )));
        }
    }catch(Exception $e) {
        die(json_encode(array(
            "message" => "unable to parse url"
        )));
    }

    echo json_encode(StatisticService::getStatistic($url, $element, $domain));
}