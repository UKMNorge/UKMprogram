<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;

require_once('UKM/Autoloader.php');


$arrangement = new Arrangement( get_option('pl_id') );
$hendelseId = isset($_POST['hendelse']) ? (int) $_POST['hendelse'] : 0;
$success = false;

if ($hendelseId > 0) {
	$hendelse = $arrangement->getProgram()->get($hendelseId);

	if ($hendelse) {
		try {
			if (method_exists($hendelse, 'getItems')) {
				$items = $hendelse->getItems();

				if ($items instanceof Traversable) {
					$items = iterator_to_array($items);
				}

				if (is_array($items) && !empty($items)) {
					Write::redefineOrder($hendelse, array_reverse($items));
					$success = true;
				}
			}

			if (!$success) {
				$success = Write::setRekkefolgeMotsatt($hendelse);
			}
		} catch (Throwable $e) {
			$success = false;
		}
	}
}

UKMprogram::addResponseData( 'success', $success );