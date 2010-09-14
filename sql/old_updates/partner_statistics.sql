#statistics 

#wiki
select entry.partner_id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,8,10) as version,partner.created_at, 
sum(entry.media_type=1) as vid,sum(entry.media_type=2) as img,sum(entry.media_type=5) as aud,sum(entry.views) as views,sum(entry.plays) as plays
from entry,partner where entry.partner_id=partner.id and partner.description like("%wiki%") group by entry.partner_id;

#empty wikies
select partner.id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,8,10) as version,partner.created_at,sum(entry.type=1),count(entry.id)
from partner left join entry on partner.id=entry.partner_id where partner.description like("%wiki%") group by partner.id having count(entry.id)<1;

#wordpress
select entry.partner_id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,1,40) as description,partner.created_at, 
sum(entry.media_type=1) as vid,sum(entry.media_type=2) as img,sum(entry.media_type=5) as aud,sum(entry.views) as views,sum(entry.plays) as plays
from entry,partner where entry.partner_id=partner.id and partner.description like("%wordpress%") group by entry.partner_id;

#empty wordpress
select partner.id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,8,10) as version,partner.created_at,sum(entry.type=1),count(entry.id)
from partner left join entry on partner.id=entry.partner_id where partner.description like("%wordpress%") group by partner.id having count(entry.id)<1;

#managed accounts
select entry.partner_id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,1,40) as description,partner.created_at, 
sum(entry.media_type=1) as vid,sum(entry.media_type=2) as img,sum(entry.media_type=5) as aud,sum(entry.views) as views,sum(entry.plays) as plays
from entry,partner where entry.partner_id=partner.id and partner.id in (387,530) group by entry.partner_id;

select partner.id,partner.partner_name,partner.admin_name,partner.admin_email,partner.url1,substr(partner.description,8,10) as version,partner.created_at,sum(entry.type=1),count(entry.id)
from partner left join entry on partner.id=entry.partner_id where partner.id in (387,530) group by partner.id;

#kshows - count the kshows with 
select kshow.partner_id,sum(kshow.entries>2) from kshow group by kshow.partner_id;

#plays and views
select partner.id,sum(widget_log.plays) as plays,sum(widget_log.views) as views from partner,widget_log where partner.id=widget_log.partner_id group by partner.id;



# when using the partner_stats table:

insert into partner_stats ( partner_id,videos,images,audios,views,plays,created_at) 
select entry.partner_id,
sum(entry.media_type=1) as vid,sum(entry.media_type=2) as img,sum(entry.media_type=5) as aud,sum(entry.views) as views,sum(entry.plays) as plays,
now()
from entry,partner where entry.partner_id=partner.id  group by entry.partner_id ;

update partner_stats as ps,partner as p set 
	ps.views=(select sum(widget_log.views) from widget_log where widget_log.partner_id=p.id group by widget_log.partner_id),
	ps.plays=(select sum(widget_log.plays) from widget_log where widget_log.partner_id=p.id group by widget_log.partner_id),
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
