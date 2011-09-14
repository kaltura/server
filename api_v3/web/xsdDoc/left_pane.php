<?php 
	$config = new Zend_Config_Ini("../../config/testme.ini", null, array('allowModifications' => true));
	$config = KalturaPluginManager::mergeConfigs($config, 'xsddoc');
	$indexConfig = $config->get('xsddoc');
	
	$exclude = explode(',', $indexConfig->get("exclude"));
	$schemaReflector = KalturaTypeReflectorCacher::get('KalturaSchemaType');
	$schemas = $schemaReflector->getConstants();
?>
<div class="left">
	<div class="left-content">
		<div id="general">
			<h2>General</h2>
			<ul>
				<li><a href="?page=overview">Overview</a></li>
			</ul>
		</div>

		<div id="schemas">
			<h2>Schemas</h2>
			<ul class="schemas">
			<?php foreach($schemas as $schema): ?>
				<li class="schema">
					<a href="?type=<?php echo $schema->getDefaultValue(); ?>"><?php echo $schema->getDescription(); ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
	</div>
</div>
