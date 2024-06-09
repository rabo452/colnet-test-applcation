<?php 

// service for parsing html
class HtmlParserService {
    private $xpath;
    function __construct($html) {
        $dom = new DOMDocument();

        // Suppress warnings due to malformed HTML
        @$dom->loadHTML($html);

        // Create a new DOMXPath
        $this->xpath = new DOMXPath($dom);
    }
    
    public function getElementsCount(string $element): int {
        // Query all tags
        $elements = $this->xpath->query("//" . $element);

        return count($elements);
    } 
}