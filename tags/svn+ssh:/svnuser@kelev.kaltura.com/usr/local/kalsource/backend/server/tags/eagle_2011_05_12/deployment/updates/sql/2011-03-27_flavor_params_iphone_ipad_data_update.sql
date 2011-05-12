# for 306 records
update flavor_params
  set 
    updated_at=now(), tags="mobile,iphone,h264,web,iphonenew",
    gop_size=50,
#      width=480,
    conversion_engines="2,99", 
    conversion_engines_extra_params='-flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2  -vsync 2 | -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2 -vsync 2'
  where isnull(deleted_at) and partner_id!=-1 and tags not like '%iphonenew%' and tags like '%iphone%'  
    and name = 'Mobile (H264)'
    and frame_rate=25;

# for 23 records
update flavor_params
  set 
    updated_at=now(), tags="mobile,iphone,h264,web,iphonenew",
    gop_size=50,
#      width=480,
    conversion_engines="2,99", 
    conversion_engines_extra_params='-flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2  -vsync 2 | -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2 -vsync 2'
  where isnull(deleted_at) and partner_id!=-1 and tags not like '%iphonenew%' and tags like '%iphone%'  
    and frame_rate=25
    and (tags='iphone,web' or tags='iphone' or tags='web,iphone,android');

# for 13 records - test accounts?
update flavor_params
  set 
    updated_at=now(), tags="mobile,iphone,h264,web,iphonenew",
    gop_size=50,
#      width=480,
    conversion_engines="2,99", 
    conversion_engines_extra_params='-flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2  -vsync 2 | -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 400k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2 -vsync 2'
  where isnull(deleted_at) and partner_id!=-1 and tags not like '%iphonenew%' and tags like '%iphone%'  
    and frame_rate=25
  and partner_id in(308862,336081,225802);

#ipad - 310
update flavor_params
  set 
    updated_at=now(), tags="web,ipad,ipadnew",
    gop_size=50, frame_rate=25,
    conversion_engines="2,99", 
    conversion_engines_extra_params='-flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2  -vsync 2 | -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k -maxrate 1200k -bufsize 1200k -rc_eq ''blurCplx^(1-qComp)'' -level 30 -async 2 -vsync 2'
  where isnull(deleted_at) and partner_id!=-1 and tags not like '%ipadnew%' and tags like '%ipad%'  
    and frame_rate in (0,25)
    and name='iPad';
