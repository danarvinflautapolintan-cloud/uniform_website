ALTER TABLE uniform_sizes
    ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER stock;

UPDATE uniform_sizes s
JOIN uniforms u ON u.id = s.uniform_id
SET s.price = u.price;

ALTER TABLE requests
    ADD COLUMN price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER size_id;

UPDATE requests r
JOIN uniform_sizes s ON s.id = r.size_id
SET r.price = s.price;
