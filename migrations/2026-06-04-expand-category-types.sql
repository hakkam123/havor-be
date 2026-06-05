START TRANSACTION;

ALTER TABLE categories
  MODIFY type ENUM('news', 'product', 'News', 'Career', 'Campaign', 'Product') NOT NULL DEFAULT 'Product';

UPDATE categories SET type = 'News' WHERE type = 'news';
UPDATE categories SET type = 'Product' WHERE type = 'product';

ALTER TABLE categories
  MODIFY type ENUM('News', 'Career', 'Campaign', 'Product') NOT NULL DEFAULT 'Product';

COMMIT;
