<?php
function UKMdeltakere_ajax_view($c){
	$kommuner = $c['kommuner'];
	$person = $c['person'];
	$innslag = $c['innslag'];
?> 
<form class="edit_person_form">
	<fieldset>
		<legend>Endre detaljer for "navn"</legend>
		<div class="group">
			<label class="first_name_label" for="fornavn">Fornavn</label>
			<input class="first_name_input" type="text" name="p_firstname" value="<?=$person->g('p_firstname')?>" />
			<input type="hidden" name="log_current_value_p_firstname" value="<?=$person->g('p_firstname')?>" />
		</div>	
		<div class="group">	
			<label class="last_name_label" for="etternavn">Etternavn</label>
			<input class="last_name_input" type="text" name="p_lastname" value="<?=$person->g('p_lastname')?>" />	
			<input type="hidden" name="log_current_value_p_lastname" value="<?=$person->g('p_lastname')?>" />	
		</div>
		<div class="group">
			<label class="age_label" for="alder">Alder</label>
			<select class="age_select" name="p_dob">
				<?php 
				$year = date('Y');
				for($i = 10; $i<=26; ++$i) {
					$date = '1 January ' . ($year-$i);
					$timestamp = strtotime( $date );
				?>
				<option <?php if( $person->getAge() == $i ) echo 'selected' ?> value="<?=$timestamp?>"><?=$i . ' &aring;r'?></option>
				<?php } ?>
			</select>
            <input type="hidden" name="log_current_value_p_dob" value="<?=$person->g('p_dob')?>" />	
		</div>	
		
		<div class="group">
			<label class="cellphone_label" for="mobil">Mobil</label>
			<input class="cellphone_input" maxlength="8" type="text" name="p_phone" value="<?=$person->g('p_phone')?>" />		
  			<input type="hidden" name="log_current_value_p_phone" value="<?=$person->g('p_phone')?>" />		
		</div>
		<?php
		 if($innslag->g('bt_id')==5||$innslag->g('bt_id')==9) { ?>
		<div class="group">
			<label class="role_label" for="rolle">Funksjon</label>
			<input class="role_input" type="text" name="instrument" value="<?=$person->g('instrument')?>" />		
            <input type="hidden" name="log_current_value_instrument" value="<?=$person->g('instrument')?>" />		
		</div>
		
		<div class="clear"></div>
		<?php } ?>
		<div class="group">
			<label class="adress_label" for="adresse">Adresse</label>
			<input class="adress_input" type="text" name="p_adress" value="<?=$person->g('p_adress')?>" />
			<input type="hidden" name="log_current_value_p_adress" value="<?=$person->g('p_adress')?>" />
		</div>
		
		<div class="group">
			<label class="postal_label" for="postnr">Postnr</label>
			<input class="postal_input" maxlength="4" type="text" name="p_postnumber" value="<?=$person->g('p_postnumber')?>" />
            <input type="hidden" name="log_current_value_p_postnumber" value="<?=$person->g('p_postnumber')?>" />
		</div>
		
        <?php if (sizeof($kommuner) > 1) { ?>
            <div class="group">
                <label class="county_label" for="kommune">Kommune</label>
                <select class="county_input" name="p_kommune">
                <?php foreach( $kommuner as $key => $kommune ) {?>
                    <option <?php if( $person->g('p_kommune') == $key ) echo 'selected' ?> value="<?=$key?>"><?=$kommune?></option>	
                <?php } ?>
                </select>
                <input type="hidden" name="log_current_value_p_kommune" value="<?=$person->g('p_kommune')?>" />
            </div>
        <?php } ?>
		
		<div class="group">
			<label class="email_label" for="email">E-mail</label>
			<input class="email_input" type="text" name="p_email" value="<?=$person->g('p_email')?>" />	
   			<input type="hidden" name="log_current_value_p_email" value="<?=$person->g('p_email')?>" />	
		</div>
		
		<input type="hidden" name="p" value="<?=$person->g('p_id')?>" />
        <input type="hidden" name="b" value="<?=$person->g('b_id')?>" />
		
		<div class="erfaring_wrapper">	
			<label class="erfaring_label" for="erfaring">Erfaring</label>
			<textarea class="erfaring_text" name="erfaring"><?=$innslag->g('td_konferansier')?></textarea>
			<input type="hidden" name="log_current_value_erfaring" value="<?=$innslag->g('td_konferansier')?>" />
		</div>
		
		<div class="clear"></div>
        <?=ukmdeltakere_ajax_buttons('person',$person->g('p_id'));?>		
			
	</fieldset>
</form>

<?php } ?>
