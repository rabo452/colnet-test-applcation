<?php

class GeneralStatistic {
    private string $domain;
    private string $html_element; 
    private int $urls_count; # how many urls of that domain have been checked until now?
    private int $today_fetch_time; # what time does it take to get request in the last 24 hours (in miliseconds)? 
    private int $domain_tag_count; # what is the count of tags (html_element) that have been spotted in this domain?
    private int $overall_tag_count; # what is the count of tags that have been spotted at all?

    // Constructor
    public function __construct(string $domain, string $html_element, int $urls_count, int $today_fetch_time, int $domain_tag_count, int $overall_tag_count) {
        $this->domain = $domain;
        $this->html_element = $html_element;
        $this->urls_count = $urls_count;
        $this->today_fetch_time = $today_fetch_time;
        $this->domain_tag_count = $domain_tag_count;
        $this->overall_tag_count = $overall_tag_count;
    }

    // Getter for domain
    public function getDomain(): string {
        return $this->domain;
    }

    // Getter for html_element
    public function getHtmlElement(): string {
        return $this->html_element;
    }

    // Getter for urls_count
    public function getUrlsCount(): int {
        return $this->urls_count;
    }

    // Getter for today_fetch_time
    public function getTodayFetchTime(): int {
        return $this->today_fetch_time;
    }

    // Getter for domain_tag_count
    public function getDomainTagCount(): int {
        return $this->domain_tag_count;
    }

    // Getter for overall_tag_count
    public function getOverallTagCount(): int {
        return $this->overall_tag_count;
    }
}