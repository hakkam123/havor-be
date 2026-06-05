START TRANSACTION;

ALTER TABLE categories
  ADD COLUMN type ENUM('news', 'product') NOT NULL DEFAULT 'product' AFTER name;

ALTER TABLE categories DROP INDEX name;

CREATE UNIQUE INDEX categories_type_name_unique ON categories(type, name);
CREATE INDEX categories_type_idx ON categories(type);

INSERT INTO categories (name, type, createdAt, updatedAt)
SELECT DISTINCT TRIM(category), 'news', NOW(), NOW()
FROM news
WHERE category IS NOT NULL AND TRIM(category) != ''
ON DUPLICATE KEY UPDATE updatedAt = VALUES(updatedAt);

CREATE TABLE IF NOT EXISTS campaigns (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  category VARCHAR(255) DEFAULT NULL,
  is_published TINYINT(1) DEFAULT 0,
  createdAt DATETIME NOT NULL,
  updatedAt DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY campaigns_published_created_idx (is_published, createdAt),
  KEY campaigns_slug_idx (slug),
  KEY campaigns_category_idx (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX news_published_created_idx ON news(is_published, createdAt);
CREATE INDEX news_category_idx ON news(category);
CREATE INDEX products_category_created_idx ON products(categoryId, createdAt);
CREATE INDEX works_category_created_idx ON works(categoryId, createdAt);

COMMIT;
