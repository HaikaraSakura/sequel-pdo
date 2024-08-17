CREATE DATABASE IF NOT EXISTS test_db;

CREATE user 'adminer'@'%' IDENTIFIED WITH mysql_native_password BY 'adminer';
GRANT ALL ON *.* TO 'adminer'@'%';

use test_db;

CREATE TABLE
    IF NOT EXISTS items (
        id INT AUTO_INCREMENT,
        item_name VARCHAR(255) NOT NULL,
        category_id INT NOT NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    )
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
    ENGINE=InnoDB;

INSERT IGNORE INTO items (id, item_name, category_id) VALUES
    (1, 'item1', 1),
    (2, 'item2', 2),
    (3, 'item3', 3);
