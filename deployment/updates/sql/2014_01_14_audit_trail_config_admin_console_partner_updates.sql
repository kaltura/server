UPDATE audit_trail_config
        SET descriptors='extendedFreeTrailExpiryDate,extendedFreeTrailExpiryReason,partner.STATUS,statusChangeReason'
        WHERE partner_id = -2
                AND object_type = 'Partner'
                AND descriptors = 'extendedFreeTrailExpiryDate,extendedFreeTrailExpiryReason'
        ;
