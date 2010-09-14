# when using the partner_stats table:

#get rid of all the existing data
truncate table partner_stats;

#READY entries (global count,videos,images,audios) of type clip
insert into partner_stats ( partner_id,entries,videos,images,audios,views,plays,created_at) 
select entry.partner_id,
	count(1) as entries,
	sum(entry.media_type=1) as vid,sum(entry.media_type=2) as img,sum(entry.media_type=5) as aud,
	sum(entry.views) as views,sum(entry.plays) as plays,
	now()
from entry,partner where entry.partner_id=partner.id and entry.status=2 and entry.type=1 group by entry.partner_id ;

# make sure plays/views are 0 if null so the next calculation will work roperly
update partner_stats as ps set ps.views=0 where ps.views is null;
update partner_stats as ps set ps.plays=0 where ps.plays is null;
	
# we should add the number of views/plays from the entries to those on the widget_log for now
update partner_stats as ps,partner as p set 
	ps.views=ps.views+IFNULL((select sum(widget_log.views) from widget_log where widget_log.partner_id=p.id group by widget_log.partner_id),0),
	ps.plays=ps.plays+IFNULL((select sum(widget_log.plays) from widget_log where widget_log.partner_id=p.id group by widget_log.partner_id),0),
	ps.updated_at=now()
where ps.partner_id=p.id;

# get number of widgets for each partner
update partner_stats as ps,partner as p set 
	ps.widgets=(select count(1) from widget where widget.partner_id=p.id group by widget.partner_id),
	ps.updated_at=now()
where ps.partner_id=p.id;

update partner_stats as ps,partner as p set 
	ps.users_1=(select sum(puser_kuser.id) from puser_kuser where puser_kuser.partner_id=p.id group by puser_kuser.partner_id),
	ps.updated_at=now()
where ps.partner_id=p.id;

update partner_stats as ps,partner as p set 
	ps.rc_1=(select sum(kshow.entries) from kshow where kshow.partner_id=p.id and kshow.entries>1 group by kshow.partner_id),
	ps.updated_at=now()
where ps.partner_id=p.id;

update partner_stats as ps,partner as p set 
	ps.users_1=(select count(1) from kuser where kuser.partner_id=p.id group by kuser.partner_id),
	ps.users_2=(select sum(kuser.entries>0) from kuser where kuser.partner_id=p.id group by kuser.partner_id),	
	ps.updated_at=now()
where ps.partner_id=p.id;

#kshows
update partner_stats as ps,partner as p set 
	ps.kshows_1=(select count(1) from kshow where kshow.partner_id=p.id group by kshow.partner_id),
	ps.updated_at=now()
where ps.partner_id=p.id;

