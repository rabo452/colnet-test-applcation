<?php

require_once ROOT_DIR . '/models/RequestStatistic.php';

interface DatabaseManager {
    public function getUrlsCountDomain(string $domain): int;
    public function getTodayAvgDomainsFetchTime(string $domain): int;
    public function getDomainElementsCount(string $domain, string $element_tag): int;
    public function getElementsCount(string $element_tag): int;
    public function wasThereTheSameRequest(string $url, string $element_tag): ?RequestStatistic;
    public function insertElementIfNotExists(string $element_tag): void;
    public function insertDomainIfNotExists(string $domain): void;
    public function insertUrlIfNotExists(string $url): void;
    public function insertRequest(RequestStatistic $statistic): void;
}
