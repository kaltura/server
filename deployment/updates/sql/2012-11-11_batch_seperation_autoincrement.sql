INSERT INTO batch_job_sep (id, STATUS)
SELECT MAX(id) + 10000 AS mx, 11 AS stat
FROM batch_job
UNION
SELECT MAX(id) + 10000 AS mx, 11 AS stat
FROM batch_job_sep
ORDER BY mx DESC 
LIMIT 1;