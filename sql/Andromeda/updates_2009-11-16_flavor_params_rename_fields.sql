ALTER TABLE flavor_params DROP conversion_engine;
ALTER TABLE flavor_params DROP conversion_engine_extra_params;
ALTER TABLE flavor_params ADD conversion_engines VARCHAR(1024) AFTER gop_size;
ALTER TABLE flavor_params ADD conversion_engines_extra_params VARCHAR(1024) AFTER conversion_engines;

ALTER TABLE flavor_params_output DROP conversion_engine;
ALTER TABLE flavor_params_output DROP conversion_engine_extra_params;
ALTER TABLE flavor_params_output ADD conversion_engines VARCHAR(1024) AFTER gop_size;
ALTER TABLE flavor_params_output ADD conversion_engines_extra_params VARCHAR(1024) AFTER conversion_engines;