# KALTURA-INSTALL
export KALTURA_ROOT_DIR="$(cd "${0%/*}" 2>/dev/null; echo "$PWD")/../../../"
export KALTURA_BATCH_RULES_PATH=$KALTURA_ROOT_DIR/kaltura/alpha/batch/ce_rules.cfg
export KALTURA_BATCH_LOCK_PATH=$KALTURA_ROOT_DIR/kaltura/alpha/batch/pid
export PHP_PATH=php


