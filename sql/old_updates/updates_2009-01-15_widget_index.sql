# add indexes to widget and widget_log
alter table widget_log add index widget_index (widget_type);
alter table widget add index created_at_index (created_at);
