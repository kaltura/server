<?php decorate_with(sfLoader::getTemplatePath('default', 'defaultLayout.php')) ?>

<div class="sfTMessageContainer sfTAlert"> 
  <?php echo image_tag('/sf/sf_default/images/icons/tools48.png', array('alt' => 'website unavailable', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>
  <div class="sfTMessageWrap">
    <h1>Website Currently Unavailable</h1>
    <h5>This website has been temporarily disabled. Please try again later.</h5>
  </div>
</div>
