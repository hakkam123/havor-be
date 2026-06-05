ALTER TABLE careers
  ADD COLUMN categoryId INT(11) DEFAULT NULL AFTER job_description,
  ADD KEY careers_category_created_idx (categoryId, createdAt),
  ADD CONSTRAINT careers_category_fk
    FOREIGN KEY (categoryId) REFERENCES categories (id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;
