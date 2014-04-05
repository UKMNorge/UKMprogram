<?php

require_once('UKM/monstring.class.php');
require_once('UKM/forestilling.class.php');

$m = new monstring( get_option('pl_id') );

$hendelser = $m->hendelser();

var_dump( $hendelser );