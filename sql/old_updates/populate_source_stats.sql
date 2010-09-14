#
select e.media_type,e.source,e.partner_id,count(1) 
from entry as e where e.media_type in (1,2,5) group by e.partner_id,e.media_type,e.source;
