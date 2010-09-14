alter table widget add  column `security_policy` SMALLINT;
alter table widget add  column `partner_data` VARCHAR(4096);
alter table kuser add  column `partner_data` VARCHAR(4096);
alter table kshow add  column `partner_data` VARCHAR(4096);
alter table entry add  column `partner_data` VARCHAR(4096);


    