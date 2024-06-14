<?php 

function getDomainFromUrl($url) {
    // Extract domain using regex
    preg_match('/https?:\/\/(.[^\/]+)/', $url, $matches);
    
    if (isset($matches[1])) {
        return $matches[1];
    } 
    throw new Exception("unable to get domain from this url: $url");
}
