<?php 

class RequestResult {
    private string $response_time;
    private string $response;
    private string $date;

    // Constructor to initialize properties
    function __construct(string $response_time, string $response, string $date) {
        $this->response_time = $response_time;
        $this->response = $response;
        $this->date = $date;
    }

    // Getter for response_time
    public function getResponseTime(): string {
        return $this->response_time;
    }

    // Getter for response
    public function getResponse(): string {
        return $this->response;
    }

    // Getter for date
    public function getDate(): string {
        return $this->date;
    }
}
