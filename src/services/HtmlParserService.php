<?php 

// service for parsing html
class HtmlParserService {    
    public static function getElementsCount(string $element): int {
        $dom = new DOMDocument();

        // Suppress warnings due to malformed HTML
        @$dom->loadHTML($html);

        // Create a new DOMXPath
        $xpath = new DOMXPath($dom);
        
        // Query all tags
        $elements = $xpath->query("//" . $element);

        return count($elements);
    } 
}
