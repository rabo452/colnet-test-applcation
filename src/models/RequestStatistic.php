<?php 

// data class for holding request statistic about request
class RequestStatistic {
    private string $response_time; // response time (time between sending and receiving the response)
    private string $element_tag; // element tag that was counted
    private string $request_date; // the date when the request was made   
    private string $url; // url user provided
    private int $element_tag_count; // count of tags in the url

    public function __construct(string $response_time, string $element_tag, string $request_date, int $element_tag_count, string $url) {
        $this->response_time = $response_time;
        $this->element_tag = $element_tag;
        $this->request_date = $request_date;
        $this->element_tag_count = $element_tag_count;
        $this->url = $url;
    }

    // Getter for response_time
    public function getResponseTime(): string {
        return $this->response_time;
    }

    // Getter for element_tag
    public function getElementTag(): string {
        return $this->element_tag;
    }

    // Getter for request_date
    public function getRequestDate(): string {
        return $this->request_date;
    }

    // Getter for element_tag_count
    public function getElementTagCount(): int {
        return $this->element_tag_count;
    }

    public function getUrl(): string {
        return $this->url;
    }
}
