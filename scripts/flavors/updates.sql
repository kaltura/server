/* High Definition */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'HD',
		tags = 'web,mbr',
		description = 'High Definition',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '4000',
		audio_codec = 'mp3',
		audio_bitrate = '192',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '1080',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '--FE2_VP6_MIN_Q=5',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'HD'
AND		partner_id = 0
AND		deleted_at is null;

/* High web quality, large frame */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'High - Large',
		tags = 'web,mbr',
		description = 'High web quality, large frame',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '2500',
		audio_codec = 'mp3',
		audio_bitrate = '128',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '--FE2_VP6_MIN_Q=5',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'High - Large'
AND		partner_id = 0
AND		deleted_at is null;

/* Standard web quality, large frame */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Standard - Large',
		tags = 'web,mbr',
		description = 'Standard web quality, large frame',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '1350',
		audio_codec = 'mp3',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Standard - Large'
AND		partner_id = 0
AND		deleted_at is null;

/* Standard web quality, small frame */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Standard - Small',
		tags = 'web,mbr',
		description = 'Standard web quality, small frame',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '750',
		audio_codec = 'mp3',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '288',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Standard - Small'
AND		partner_id = 0
AND		deleted_at is null;

/* Basic web quality, small frame */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Basic - Small',
		tags = 'web,mbr',
		description = 'Basic web quality, small frame',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '400',
		audio_codec = 'mp3',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '288',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Basic - Small'
AND		partner_id = 0
AND		deleted_at is null;

/* High web quality in MP4 format, for download or syndication */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'HQ MP4 for Export',
		tags = 'mp4_export,web',
		description = 'High web quality in MP4 format, for download or syndication',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '2500',
		audio_codec = 'aac',
		audio_bitrate = '128',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '1,4,2',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'HQ MP4 for Export'
AND		partner_id = 0
AND		deleted_at is null;

/* Audio-only */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Audio-only',
		tags = 'audio_only_export,web',
		description = 'Audio-only',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = '',
		video_bitrate = '0',
		audio_codec = 'mp3',
		audio_bitrate = '96',
		audio_channels = '2',
		audio_sample_rate = '44100',
		audio_resolution = '0',
		width = '0',
		height = '0',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,99',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Audio-only'
AND		partner_id = 0
AND		deleted_at is null;

/* Good web quality, for editable content */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Editable',
		tags = 'edit,web',
		description = 'Good web quality, for editable content',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = 'flv',
		video_codec = 'vp6',
		video_bitrate = '700',
		audio_codec = 'mp3',
		audio_bitrate = '64',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '480',
		frame_rate = '0',
		gop_size = '5',
		two_pass = '0',
		conversion_engines = '1,4,2,99,3',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Editable'
AND		partner_id = 0
AND		deleted_at is null;

/* Maintains the original format and settings of the file – duplicate of the source file */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'Source',
		tags = 'source',
		description = 'Maintains the original format and settings of the file – duplicate of the source file',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = '',
		video_codec = '',
		video_bitrate = '0',
		audio_codec = '',
		audio_bitrate = '0',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '0',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Source'
AND		partner_id = 0
AND		deleted_at is null;

/* High web quality in AVI format, for download or syndication */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'HQ AVI for Export',
		tags = 'avi_export',
		description = 'High web quality in AVI format, for download or syndication',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = 'avi',
		video_codec = 'h264',
		video_bitrate = '2500',
		audio_codec = 'mp3',
		audio_bitrate = '128',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,3,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'HQ AVI for Export'
AND		partner_id = 0
AND		deleted_at is null;

/* High web quality in MOV format, for download or syndication */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '0',
		name = 'HQ MOV for Export',
		tags = 'mov_export',
		description = 'High web quality in MOV format, for download or syndication',
		ready_behavior = '0',
		deleted_at = NULL,
		is_default = '1',
		format = 'mov',
		video_codec = 'h264',
		video_bitrate = '2500',
		audio_codec = 'aac',
		audio_bitrate = '128',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,3,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'HQ MOV for Export'
AND		partner_id = 0
AND		deleted_at is null;

/*  */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '-1',
		name = 'Basic - Small (H264)',
		tags = 'web,mbr',
		description = '',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '0',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '400',
		audio_codec = 'aac',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '288',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,1,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Basic - Small (H264)'
AND		partner_id = 0
AND		deleted_at is null;

/*  */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '-1',
		name = 'Standard - Small (H264)',
		tags = 'web,mbr',
		description = '',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '0',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '750',
		audio_codec = 'aac',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '288',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,1,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Standard - Small (H264)'
AND		partner_id = 0
AND		deleted_at is null;

/*  */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '-1',
		name = 'Standard - Large (H264)',
		tags = 'web,mbr\
',
		description = '',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '0',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '1350',
		audio_codec = 'aac',
		audio_bitrate = '96',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,1,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'Standard - Large (H264)'
AND		partner_id = 0
AND		deleted_at is null;

/*  */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '-1',
		name = 'High - Large (H264)',
		tags = 'web,mbr	\
',
		description = '',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '0',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '2500',
		audio_codec = 'aac',
		audio_bitrate = '128',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '720',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,1,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'High - Large (H264)'
AND		partner_id = 0
AND		deleted_at is null;

/*  */
UPDATE 	flavor_params
SET		version = '0',
		partner_id = '-1',
		name = 'HD (H264)',
		tags = 'web,mbr',
		description = '',
		ready_behavior = '2',
		deleted_at = NULL,
		is_default = '0',
		format = 'mp4',
		video_codec = 'h264',
		video_bitrate = '4000',
		audio_codec = 'aac',
		audio_bitrate = '192',
		audio_channels = '0',
		audio_sample_rate = '0',
		audio_resolution = '0',
		width = '0',
		height = '1080',
		frame_rate = '0',
		gop_size = '0',
		two_pass = '0',
		conversion_engines = '2,1,4',
		conversion_engines_extra_params = '',
		custom_data = NULL,
		view_order = '0',
		creation_mode = '1'
WHERE	name = 'HD (H264)'
AND		partner_id = 0
AND		deleted_at is null;

