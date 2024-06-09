<?php 

require_once ROOT_DIR . '/models/RequestResult.php';

// service for making http requests
class RequestService {
    public static function fetchPage(string $request_url): RequestResult {
        $url = $request_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set the option to return the response as a string instead of outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // Set the user-agent
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");

        $start_time = microtime(true);

        // Execute the request
        $response = curl_exec($ch);

        // Stop tracking time after the request has completed
        $end_time = microtime(true);

        $response_time = $end_time - $start_time;
        $response_time = number_format($response_time, 3);
        $response_time *= 1000;

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception($error);
        }

        curl_close($ch);

        // return result as object with defined fields
        return new RequestResult($response_time, $response, date('d/m/Y H:i'));
    }
}