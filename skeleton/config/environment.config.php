<?php

// System configuration. This config should work as it is provided.
// MAKE NO CHANGES UNLESS YOU KNOW WHAT YOU ARE DOING
define('LOCAL_TIME_ZONE', 'Europe/Berlin');

define('SMARTY_SPL_AUTOLOAD', 1);
define('SMARTY_DIR', PATH_LIBRARIES . DS . "smarty");
define('SMARTY_PLUGINS_DIR', SMARTY_DIR . DS . "plugins" . DS);
define('SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . DS . "sysplugins" . DS);