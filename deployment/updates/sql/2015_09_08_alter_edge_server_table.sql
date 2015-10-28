ALTER TABLE `edge_server` 
ADD `dc` int(11)
AFTER `id`;

ALTER TABLE `edge_server`
ADD `heartbeat_time` DATETIME
AFTER `updated_at`;