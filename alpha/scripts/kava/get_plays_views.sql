select entry_id, UNIX_TIMESTAMP(last_played_at), plays, views
from kava.kava_plays_views;
