<?php 
	$config = new Zend_Config_Ini("../../config/testme.ini", null, array('allowModifications' => true));
	$config = KalturaPluginManager::mergeConfigs($config, 'xsddoc');
	$indexConfig = $config->get('xsddoc');
	
	$exclude = explode(',', $indexConfig->get("exclude"));
	$schemaReflector = KalturaTypeReflectorCacher::get('KalturaSchemaType');
	$schemas = $schemaReflector->getConstantsValues();
?>
<div class="left">
	<div class="left-content">
		<div id="general">
			<h2>General</h2>
			<ul>
				<li><a href="?page=overview">Overview</a></li>
				<li><a href="?page=terminology">Terminology</a></li>
			</ul>
		</div>

		<div id="schemas">
			<h2>Schemas</h2>
			<ul class="schemas">
			<?php foreach($schemas as $schemaName => $value): ?>
				<li class="schema">
					<a href="?type=<?php echo $value; ?>"><?php echo ucwords(strtolower(str_replace('_', ' ', $schemaName))); ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		
	</div>
</div>
