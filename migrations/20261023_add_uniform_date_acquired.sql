ALTER TABLE uniforms
    ADD COLUMN IF NOT EXISTS date_acquired DATE NULL AFTER description;

UPDATE uniforms
SET date_acquired = DATE(created_at)
WHERE date_acquired IS NULL;
