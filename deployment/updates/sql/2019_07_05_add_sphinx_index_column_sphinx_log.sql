ALTER TABLE kaltura_sphinx_log.sphinx_log ADD type INT AFTER created_at;
ALTER TABLE kaltura_sphinx_log.sphinx_log ADD index_name VARCHAR(128) AFTER type;
