<?php 

require_once ROOT_DIR . '/db/DatabaseManager.php';
include_once ROOT_DIR . '/utils/getDomainFromUrl.php';

// manager for making requests to mysql database
class MySqlDatabaseManager implements DatabaseManager {
    private mysqli $connection; 
    // make a connection
    function __construct() {
        global $db_host, $db_username, $db_password, $db_name;
        $this->connection = new mysqli($db_host, $db_username, $db_password, $db_name);

        if ($this->connection->connect_error) {
            throw new Exception($this->connection->connect_error);
        }
    }

    # get count of urls that have been fetched for the domain
    public function getUrlsCountDomain(string $domain): int {
        $sql = "SELECT COUNT(*) as count FROM url u
                JOIN domain d ON u.domain_id = d.id 
                WHERE d.name = ?";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("s", $domain); 
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'];
    }
    
    # get today's average domain's fetch time (within last 24 hours)
    public function getTodayAvgDomainsFetchTime(string $domain): int {
        $sql = "SELECT AVG(response_time) as avg FROM request r
                JOIN domain d ON d.id = r.domain_id
                WHERE d.name = ? and r.date >= NOW() - INTERVAL 1 DAY";
        
        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("s", $domain); 
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $average_response_time = $row['avg'];
        if (!$average_response_time) {
            throw new Exception("unable to get average response time for this domain: " . $domain);
        } 

        return $average_response_time;
    }

    # get total count of the tag from the domain 
    public function getDomainElementsCount(string $domain, string $element_tag): int {
        $sql = "SELECT SUM(element_count) as sum FROM 
                (SELECT AVG(element_count) as element_count FROM request r 
                JOIN domain d ON d.id = r.domain_id
                JOIN element e ON e.id = r.element_id
                WHERE d.name = ? and e.name = ?
                group by url_id
                ) as subquery;";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("ss", $domain, $element_tag);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $domain_element_count = $row['sum'];
        $stmt->close();
        if (is_null($domain_element_count)) {
            throw new Exception("unable to get elements count for the element " . $element_tag . "from this domain " . $domain);
        }

        return $domain_element_count;
    }
    
    # get total count of the tag from all requests ever made
    public function getElementsCount(string $element_tag): int {
        $sql = "SELECT SUM(element_count) as sum FROM 
                (SELECT AVG(element_count) as element_count FROM request r 
                JOIN element e ON e.id = r.element_id
                WHERE e.name = ?
                group by url_id
                ) as subquery;";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("s", $element_tag);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $domain_element_count = $row['sum'];
        $stmt->close();
        if (is_null($domain_element_count)) {
            throw new Exception("unable to get elements count for the element " . $element_tag);
        }

        return $domain_element_count; 
    }

    # check if there is a row request that was made for the same url within last 5 minutes 
    public function wasThereTheSameRequest(string $url, string $element_tag): ?RequestStatistic {
        $sql = "SELECT * 
                FROM request r 
                JOIN url u ON u.id = r.url_id
                JOIN element e ON e.id = r.element_id
                WHERE r.date >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
                AND u.name = ? 
                AND e.name = ?";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("ss", $url, $element_tag);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();
        if (!$row) {
            return null; 
        }

        return new RequestStatistic($row['response_time'], $element_tag, $row['date'], $row['element_count'], $url);
    }

    # insert element tag if it's not there 
    public function insertElementIfNotExists(string $element_tag): void {
        $sql = "INSERT INTO element(name)
                SELECT ?
                FROM dual
                WHERE NOT EXISTS (
                    SELECT name FROM element WHERE name = ?
                )";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        // Bind parameters
        $stmt->bind_param("ss", $element_tag, $element_tag);
        $stmt->execute();
        $stmt->close();
    }

    # insert the domain if it's not there 
    public function insertDomainIfNotExists(string $domain): void {
        $sql = "INSERT INTO domain(name)
                SELECT ?
                FROM dual
                WHERE NOT EXISTS (
                    SELECT name FROM domain WHERE name = ?
                )";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        // Bind parameters
        $stmt->bind_param("ss", $domain, $domain);
        $stmt->execute();
        $stmt->close();
    }
    
    # insert the url if it's not there 
    public function insertUrlIfNotExists(string $url): void {
        $domain = getDomainFromUrl($url);

        # get domain id first
        $sql = "SELECT id from domain WHERE name=?";
        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        // Bind parameters
        $stmt->bind_param("s", $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            throw new Exception("unable to find domain $domain for this url $url in database");
        }
        $domain_id = $row['id'];

        $stmt->close();

        $sql = "INSERT INTO url(name, domain_id)
                SELECT ?, ?
                FROM dual
                WHERE NOT EXISTS (
                    SELECT name FROM url WHERE name = ?
                )";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        // Bind parameters
        $stmt->bind_param("sis", $url, $domain_id, $url);
        $stmt->execute();
        $stmt->close();
    }

    public function insertRequest(RequestStatistic $statistic): void {
        # to insert request into database, we need to get domain id, element id and url id
        # get domain id and url id 
        $sql = "SELECT domain_id, id FROM url WHERE name = ?";

        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("s", $statistic->getUrl());
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("unable to get domain id for the url: {$statistic->getUrl()}");
        }

        $domain_id = $row['domain_id'];
        $url_id = $row['id'];

        # get element id 
        $sql = "SELECT id FROM element WHERE name = ?";
        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }

        $stmt->bind_param("s", $statistic->getElementTag());
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new Exception("unable to get element tag id for this tag {$statistic->getElementTag()}");
        }

        $element_id = $row['id'];

        // at last insert request into request table
        $sql = "INSERT INTO request(domain_id, url_id, element_id, element_count, date, response_time)
                VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);

        if ($stmt === false) {
            throw new Exception($this->connection->error);
        }
        
        $stmt->bind_param("iiiisi", $domain_id, $url_id, $element_id, 
                          $statistic->getElementTagCount(), 
                          date('Y-m-d H:i:s', strtotime($statistic->getRequestDate())),
                          $statistic->getResponseTime()
        );
        $stmt->execute();
    }
}