<?php
function UKMprogram_forestilling($m){
$c = new forestilling($_GET['c_id']);

$csh = date('H', $c->g('c_start'));
$csi = date('i', $c->g('c_start'));

require_once('UKM/monstring.class.php');
$monstring = new monstring_v2( get_option('pl_id') );

for($i=7;$i<24;$i++)
	$timer .= '<option value="'.$i.'" '.($csh==$i?'selected="selected"':'').'>'.($i<10?'0'.$i:$i).'</option>';
for($i=0;$i<60;$i+=5)
	$minutter .= '<option value="'.$i.'" '.($csi==$i?'selected="selected"':'').'>'.($i<10?'0'.$i:$i).'</option>';

?>
<?= UKMProg_tabs($c)?>
<form class="edit_show_form" id="forestilling_form">
<input type="hidden" name="c_id" value="<?= $_GET['c_id']?>" />
<div id="hugesubmit"><div id="lagre">Lagre</div></div>
	<fieldset>
		<div class="group">
			<label class="name_label" for="c_name">Navn <span class="red_text">*</span></label>
			<input class="name_input" type="text" name="c_name" value="<?=$c->g('c_name')?>" />
			<input type="hidden" name="log_current_value_c_name" value="<?=$c->g('c_name')?>" />
			<span class="forklaring">F.eks forestilling A, kirkekonsert osv</span>
		</div>
		<div class="group">
			<label class="description_label" for="c_place">Beskrivelse / sted) <span class="red_text">*</span></label>
			<input class="description_input" type="text" name="c_place" value="<?=$c->g('c_place')?>" />
			<input type="hidden" name="log_current_value_c_place" value="<?=$c->g('c_place')?>" />
			<span class="forklaring">F.eks Volda kulturhus, ungdomsskolen</span>
		</div>
		<div class="clearfix"></div>
		<div class="group">
			<input type="hidden" id="datepicker_start" value="<?=date('d.m.Y',$m->g('pl_start'))?>" />
			<input type="hidden" id="datepicker_stop" value="<?=date('d.m.Y',$m->g('pl_stop'))?>" />
			<label class="dato_label" for="c_start">Dato <span class="red_text">*</span></label>
			<input class="datepicker" type="text" name="c_start_datepicker" value="<?=date('d.m.Y',$c->g('c_start'))?>" />
			<input type="hidden" name="log_current_value_c_start" value="<?=$c->g('c_start')?>" />
		</div>

		<div class="group">
			<label class="time_label" for="dato_">Klokkeslett <span class="red_text">*</span></label>
			<select class="time_select" id="c_start_time" name="c_start_time"><?= $timer?></select> :
			<select class="time_select" id="c_start_min" name="c_start_min"><?= $minutter?></select>
		</div>
		
		<div class="clear"></div>
		
		<div class="group" id="rammeprogram">
			<label class="time_label" for="b_name">Synlig rammeprogram på ukm.no?</label>

			<span class="radio_group">
				<label>
					<input id="vis_ramme" type="radio" <?=($c->g('c_visible_program')=='true'?' checked="checked"':'')?> value="true" name="c_visible_program">vis i oversikt
				</label>
			</span>
			<div class="clear"></div>
			<span class="radio_group">
				<label>
					<input id="skjul_ramme" type="radio" <?=($c->g('c_visible_program')=='false'?' checked="checked"':'')?> value="false" name="c_visible_program">skjul helt
				</label>
			</span>
			<div class="forklaring">
				Skal hendelsen vises i oversikten over hendelser på din mønstring? Eller skal den være helt skjult?
			</div>
			<input type="hidden" name="log_current_value_c_visible_program" value="<?=$c->g('c_visible_program')?>" />
		</div>	
		<div class="group" id="detaljprogram">
			<label class="time_label" for="b_name">Synlig detaljprogram på ukm.no?</label>
			<span class="radio_group">
				<label>
					<input id="vis_detalj" type="radio" <?=($c->g('c_visible_detail')=='true'?' checked="checked"':'')?> value="true" name="c_visible_detail">ja
				</label>
			</span>
			<br />
			<span class="radio_group">
				<label>
					<input type="radio" <?=($c->g('c_visible_detail')=='false'?' checked="checked"':'')?> value="false" name="c_visible_detail">nei
				</label>
			</span>
			<div class="forklaring">Skal alle kunne se hvilken rekkefølge du har satt på innslagene?</div>
			<input type="hidden" name="log_current_value_c_visible_detail" value="<?=$c->g('c_visible_detail')?>" />
		</div>
	</fieldset>
	
	
	<?php 
		if( $monstring->getType() != 'kommune' ) {
	?>
	<div class="group">
		<h3>Type hendelse</h2>
		<select name="c_type" id="c_type">
			<option value="default" <?php echo $c->g('c_type') == 'default' ? 'selected="selected"' : ''; ?>>Vanlig forestilling med innslag</option>
			<option value="post" <?php echo $c->g('c_type') == 'post' ? 'selected="selected"' : ''; ?>>Egendefinert innhold fra innlegg/side</option>
			<option value="category" <?php echo $c->g('c_type') == 'category' ? 'selected="selected"' : ''; ?>>Kategori</option>
		</select>
		
		<div class="c_post" id="c_type_post" style="display:none;">
			<h3 id="c_type_post_title">Velg side / innlegg</h3>
			<select name="c_type_post_id">
				<?php
					global $wpdb;
					// A sql query to return all post titles
					$results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_status = 'publish' ORDER BY ID DESC" ), ARRAY_A );
					// Return null if we found no results
					if ( ! $results ) {
						echo '<option value="0" selected="selected" disabled="disabled">Du må ha minst én side eller post før du kan gjøre dette</option>';
					} else {
					foreach( $results as $index => $post ) {
						echo '<option value="' . $post['ID'] . '" '.( $post['ID'] == $c->get('c_type_post_id') ? 'selected="selected"':'' ) .'>' . $post['post_title'] . ' ('. ($post['post_type'] == 'post' ? 'innlegg':'side').')</option>';
					}
				}
				?>
			</select>
		</div>


		<div class="c_post" id="c_type_category" style="display:none;">
			<h3 id="c_type_category_title">Velg kategori</h3>
			<select name="c_type_category_id">
				<?php
				foreach( get_categories() as $category ) {
					echo '<option value="' . $category->term_id . '" '.( $category->term_id == $c->get('c_type_category_id') ? 'selected="selected"':'' ) .'>' . $category->name . '</option>';
				}
				?>
			</select>
		</div>

	</div>
	<div class="clearfix"></div>
	<?php 
		}
	?>
		

	<div id="c_oppmote">
		<h3 id="oppmotetid_title">Detaljer om oppmøte (avansert)</h3>
		<?php
			$before = $c->g('c_before');
			$delay = $c->g('c_delay');
			
			$oppmoteja = $oppmotenei = '';
			if($before==$delay && $delay==0)
				$oppmotenei = 'checked="checked"';
			else
				$oppmoteja = 'checked="checked"';
			
			$oppmote_before_option = '<option value="0">Når hendelsen begynner</option>';
			for($i=5; $i<(10*60); $i+=5)
				$oppmote_before_option .= '<option value="'.$i.'" '.($c->g('c_before')==$i?'selected="selected"':'').'>'.$i.' min før</option>';
	
			$oppmote_delay_option = '<option value="0">Alle møter samtidig</option>';			
			for($i=1; $i<20; $i++)
				$oppmote_delay_option .= '<option value="'.$i.'" '.($c->g('c_delay')==$i?'selected="selected"':'').'>'.$i.' min etter forrige</option>';
		?>
		<fieldset>
			<div class="group" id="oppmotetid">
				<label class="time_label" for="b_name">Vil du angi detaljer for oppmøtetid?</label>
	
				<span class="radio_group">
					<label>
						<input id="oppmote_ja" name="oppmote" type="radio" <?= $oppmoteja ?> value="true">ja
					</label>
				</span>
				<div class="clear"></div>
				<span class="radio_group">
					<label>
						<input id="oppmote_nei" name="oppmote" type="radio" <?= $oppmotenei ?> value="false">nei
					</label>
				</span>
				<div class="forklaring">Valgene du gjør her vil vises hvis du bruker rapporter med automatisk oppmøtetid</div>
			</div>	


			<div class="group" id="oppmotedetaljer" style="display:none;">
				<div class="group">
					<label class="time_label" for="oppmote_before">
						Hvor lenge før hendelsen skal det første innslaget møte?
					</label>
					<select name="c_before" id="c_before"><?= $oppmote_before_option ?></select>
					<input type="hidden" value="<?= $c->g('c_before')?>" name="log_current_value_c_before" />
				</div>

				<div class="group">
					<label class="time_label" for="oppmote_delay">
						Deretter, hvor stor tidsforskyvning skal det være per innslag?
					</label>
					<select name="c_delay" id="c_delay"><?= $oppmote_delay_option ?></select>
					<input type="hidden" value="<?= $c->g('c_delay')?>" name="log_current_value_c_delay" />
				</div>

				<div class="group" id="oppmote_vis">
					<label class="oppmote_label" for="oppmote_vis">
						Vil du vise oppmøtetid på nettsiden?
					</label>
					<div class="clearfix"></div>
					<span class="radio_group">
						<label>
							<input id="vis_oppmote_ja" name="c_visible_oppmote" type="radio" <?= ($c->g('c_visible_oppmote')=='true'?'checked="checked"':'') ?> value="true">ja
						</label>
					</span>
					<div class="clear"></div>
					<span class="radio_group">
						<label>
							<input id="vis_oppmote_ja" name="c_visible_oppmote" type="radio" <?= ($c->g('c_visible_oppmote')=='false'?'checked="checked"':'') ?> value="false">nei
						</label>
					</span>
					<div class="forklaring">Vil vises på program- og deltakersiden</div>
				</div>
			</div>
		</fieldset>
	</div>
</form>

<br />
<div class="clear clearfix clear-fix"></div>
	<input type="button" value="Dupliser denne hendelsen" id="duplicate-concert" />
	<input type="button" value="Slett denne hendelsen" id="delete-concert" />

<?php
}
?>
