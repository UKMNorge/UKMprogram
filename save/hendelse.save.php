<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Write;
use UKMNorge\Wordpress\Blog;

date_default_timezone_set('Europe/Oslo');

require_once('UKM/Autoloader.php');

$start_string = $_POST['start_date'] . '-' . $_POST['start_time'];
$start = DateTime::createFromFormat('d.m.Y-H:i', $start_string);

$arrangement = new Arrangement( get_option('pl_id') );
$hendelse = $arrangement->getProgram()->get($_POST['id']);

// BASIS-INFO
$hendelse->setNavn($_POST['navn']);
$hendelse->setSted($_POST['sted']);
$hendelse->setStart($start);
$hendelse->setSynligDetaljprogram($_POST['synlig_detalj'] == 'true');
$hendelse->setType($_POST['type']);
$hendelse->setTag($_POST['tagg']);

// HVIS ANGITT BILDE
if (isset($_FILES['bilde'])) {
    $resBilde = upload_image_to_current_blog($_FILES['bilde']);
    if ($resBilde) {
        $hendelse->setBilde($resBilde);
    } else {
        UKMprogram::getFlashbag()->add('danger', 'Kunne ikke laste opp bildet');
    }
}


if( $_POST['synlighet'] == 'deltakerprogram' ) {
    $hendelse->setSynligRammeprogram(true);
    $hendelse->setIntern(true);
} elseif( $_POST['synlighet'] == 'program' ) {
    $hendelse->setSynligRammeprogram(true);
    $hendelse->setIntern(false);
} else {
    $hendelse->setSynligRammeprogram(false);
    // Oppdaterer ikke intern, slik at en evt toggle av synlighet
    // tar innslaget tilbake til riktig state
}

// HVIS HENDELSEN HAR BESKRIVELSE
if (isset($_POST['beskrivelse'])) {
	$hendelse->setBeskrivelse($_POST['beskrivelse']);
}

// HVIS TYPE:POST
if (isset($_POST['post_id'])) {
    $blog_id = Blog::getIdByPath( $arrangement->getPath() );
    try {
        if( $_POST['post_id'] == 'createpage' ) {
            $post_id = (Int) Blog::opprettSide(
                $blog_id,
                'hendelse-'. $hendelse->getId(),
                $hendelse->getNavn()
            );
            Blog::setSideMeta( 
                $blog_id,
                $post_id,
                [
                    'hendelse' => true
                ]
            );
        } else {
            $post_id = $_POST['post_id'];
        }
        $hendelse->setTypePostId($post_id);
    } catch( Exception $e ) {
        UKMprogram::getFlashbag()->add('danger', 'Kunne ikke opprette informasjonssiden. System sa: '. $e->getMessage());
    }
}

// HVIS TYPE:KATEGORI
if (isset($_POST['category_id'])) {
	$hendelse->setTypeCategoryId($_POST['category_id']);
}

// HVIS ANGITT FARGE
if (isset($_POST['farge'])) {
	$hendelse->setFarge($_POST['farge']);
}

// HVIS ANGITT FREMHEVET
if( isset($_POST['fremhevet'])) {
	$hendelse->setFremhevet($_POST['fremhevet']=='true');
}

// EVT ANGI OPPMØTETID
if (isset($_POST['angi_oppmote']) && $_POST['angi_oppmote'] == 'true') {
	$hendelse->setOppmoteFor($_POST['oppmote_for']);
	$hendelse->setOppmoteDelay($_POST['oppmote_forskyving']);
	$hendelse->setSynligOppmotetid($_POST['oppmote_synlig'] == 'true');
} else {
	$hendelse->setOppmoteFor(0);
	$hendelse->setOppmoteDelay(0);
	$hendelse->setSynligOppmotetid(false);
}


function upload_image_to_current_blog($imageFile) {
    echo 'a';
    if ( !isset($imageFile) || $imageFile['error'] !== 0 ) {
        return false;
    }

    echo 'b';

    $file_name = sanitize_file_name( $imageFile['name'] );
    $file_temp = $imageFile['tmp_name'];

    echo 'c';

    // Get correct upload folder for current blog
    $upload_dir = wp_upload_dir();

    echo 'd';

    // Read the uploaded file
    $image_data = file_get_contents($file_temp);

    // Create a unique filename
    $filetype = wp_check_filetype($file_name);
    $filename = time() . '.' . $filetype['ext'];

    echo 'e';

    // Define the final path
    if ( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    // Save file to the filesystem
    file_put_contents( $file, $image_data );

    // Prepare attachment post
    $attachment = array(
        'post_mime_type' => $filetype['type'],
        'post_title'     => pathinfo($filename, PATHINFO_FILENAME),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    // Insert attachment into media library
    $attach_id = wp_insert_attachment( $attachment, $file );

    // Generate and update attachment metadata
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    return wp_get_attachment_url($attach_id);
}


Write::save($hendelse);

UKMprogram::getFlashbag()->add('success', $_POST['navn'] . ' lagret');
