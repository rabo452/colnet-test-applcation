<?php 

// service for getting statistic about page and general statistic 
class StatisticService {
    public static function getStatistic(string $url, string $element, string $domain): array {
        try {
            // init db
            $db_manager = new MySqlDatabaseManager();
            // try to get statistic if there was the same request recently (around 5 minutes)
            $statistic = $db_manager->wasThereTheSameRequest($url, $element);
            
            // if there wasn't such the request, then parse the page and information into database
            if (is_null($statistic)) {
                $db_manager->insertDomainIfNotExists($domain);
                $db_manager->insertElementIfNotExists($element);
                $db_manager->insertUrlIfNotExists($url, $domain);
            
                try {
                    // try to fetch a page...
                    $result = RequestService::fetchPage($url);
                }catch(Exception $e) {
                    return array(
                        "message" => "unable to fetch the page, please try the operation through some time..."
                    );
                }
                // get element count 
                $parser = new HtmlParserService($result->getResponse());
                $elements_count = $parser->getElementsCount($element);
                
                // create a statistic and save it into db
                $statistic = new RequestStatistic($result->getResponseTime(), $element, $result->getDate(), $elements_count, $url);
                $db_manager->insertRequest($statistic); 
            }
            
            // after get general statistic
            $urls_count = $db_manager->getUrlsCountDomain($domain);
            $today_avg_fetch_time = $db_manager->getTodayAvgDomainsFetchTime($domain);
            $domain_elements_count = $db_manager->getDomainElementsCount($domain, $element);
            $element_count = $db_manager->getElementsCount($element);
            
            $general_statistic = new GeneralStatistic($domain, $element, $urls_count, $today_avg_fetch_time, $domain_elements_count, $element_count);

            // return response 
            // in the real applications it'd be defined class with defined properties, but let's make it simpler 
            return array(
                "page_statistic" => array(
                    "response_time" => $statistic->getResponseTime(),
                    "element_tag" => $statistic->getElementTag(),
                    "request_date" => $statistic->getRequestDate(),
                    "tag_count" => $statistic->getElementTagCount(),
                    "url" => $statistic->getUrl()
                ),
                "general_statistic" => array(
                    "domain_urls_fetched" => $general_statistic->getUrlsCount(),
                    "today_domains_fetch_time" => $general_statistic->getTodayFetchTime(),
                    "domains_html_element_count" => $general_statistic->getDomainTagCount(),
                    "overall_html_elements_count" => $general_statistic->getOverallTagCount()
                )
            );
        }catch (Exception $e) {
            // if you wish you can add logger service right here, so you can monitor the errors
            // but as this is simple service, let's make it simpler
            return array(
                "message" => "the service is not available for a moment, please try through some time again..."
            );
        }
    }
}