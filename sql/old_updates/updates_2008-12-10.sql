# added created_at index to widget_log for faster traversal when fetching statistics
alter table widget_log add index (created_at);
