	<script type="text/javascript">
		jQuery(document).ready(function(){		
			
			function findPos(obj) {
				var curleft = curtop = 0;
				if (obj.offsetParent) {
					curleft = obj.offsetLeft
					curtop = obj.offsetTop
					while (obj = obj.offsetParent) {
						curleft += obj.offsetLeft
						curtop += obj.offsetTop
					}
				}
				return [curleft,curtop];
			}
			
			jQuery("table tbody td b").hover(function(){
					var coors = findPos(this);
					var helperwidth = jQuery("#helper").width();
					var source = "<?php echo $basePath; ?>" + this.firstChild.nodeValue;
					jQuery("#helper img").attr("src","").attr("src",source);
					jQuery("#helper").css("left",coors[0]-helperwidth-10+"px").css("top",coors[1]-20+"px").show();
				},function(){
					jQuery("#helper img").attr("src","");
					jQuery("#helper").hide();
			});
			
			jQuery("#uploadForm").submit(function() {
				jQuery(this).ajaxSubmit(function(data) { if (data == 'ok') window.location.reload(); else alert(data); });
				return false;
			} );
			
			jQuery("table tr td").find("span.btn:first").click(function(){
				if ( confirm ( "Are you certain ?" ) ){
					var row = jQuery(this).parents("tr:first");
					var fileName = row.find("td:first").text();
					jQuery.ajax({type: "DELETE", url: '/index.php/system/upload?fileName=' + fileName,
						success: function(data){ row.remove(); }
					});
				}
			}).next("span").click(function(){
				var source = "http://www.kaltura.com<?php echo $basePath; ?>" + jQuery(this).parents("tr").find("td:first b").text();
				copy(source);
			});
			
			function copy(text){
				if (text.createTextRange) {
					var range = text.createTextRange();
					if (range)
						range.execCommand('Copy');
				} else {
					var flashcopier = 'flashcopier';
					if(!$(flashcopier))
						jQuery("body").append("<div id='flashcopier'></div>");
					$(flashcopier).innerHTML = '';
					var divinfo = '<embed src="/images/flash/_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
					$(flashcopier).innerHTML = divinfo;
			  }
			}	
				
		});
		
	</script>
	<div id="wraper">
		<form id="uploadForm" method="post" action="" enctype="multipart/form-data" class="clearfix">
			<div class="item">
				<label>Upload file:</label>
				<input size="32" name="Filedata" type="file" />
				<input type="submit" value="Add New" />
			</div>
			<table>
				<colgroup></colgroup>
				<colgroup></colgroup>
				<colgroup></colgroup>
				<colgroup width="80"></colgroup>
				<thead>
					<tr>
						<td>Name</td>
						<td>Date</td>
						<td>Size</td>
						<td>Action</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($files as $file) { ?>
					<tr>
						<td><b><?php echo $file[0]; ?></b></td>
						<td><b><?php echo date('d/m/y H:i:s', $file[2]); ?></b></td>
						<td><?php echo $file[1] < 1024 ? ($file[1].' Bytes') : (floor($file[1]/1024).' Kb'); ?></td>
						<td><span class="btn">Delete</span><span class="btn">Url</span></td>
					</tr>
					<?php } ?>
				</tbody>
			</ul>
		</form>
	</div><!-- #wraper -->
	<div id="helper"><img src="" alt="" /></div>
