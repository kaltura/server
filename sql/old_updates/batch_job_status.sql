select now(),bj.id,bj.created_at as bj_created,bj.status,bj.progress,e.id as eid,e.name as ename,e.status as e_sts,e.created_at as e_created ,e.partner_id 
from batch_job as bj,entry as e where bj.entry_id=e.id and e.partner_id=2139 and (bj.status<>5 or e.status<>2);
