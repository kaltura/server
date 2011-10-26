alter table entry_distribution drop KEY partner_entry_profile, add key entry_profile(entry_id,distribution_profile_id);
