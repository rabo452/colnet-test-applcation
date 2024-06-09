-- request(id, domain_id, url_id, element_id, element_count, date, response_time) 
-- domain(id, name) 
-- url(id, name) 
-- element(id, name)

-- CREATE DATABASE colnet;

CREATE TABLE domain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE url (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    domain_id INT NOT NULL,
    FOREIGN KEY (domain_id) REFERENCES domain(id)
);

CREATE TABLE element (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE request (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    url_id INT NOT NULL,
    element_id INT NOT NULL,
    element_count INT NOT NULL,
    date DATETIME NOT NULL,
    response_time INT NOT NULL,
    FOREIGN KEY (domain_id) REFERENCES domain(id),
    FOREIGN KEY (url_id) REFERENCES url(id),
    FOREIGN KEY (element_id) REFERENCES element(id)
);