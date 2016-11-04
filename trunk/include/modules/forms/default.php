<?php
	
	/** @var hw_form $this */
	
	if( !is_array( $this->fields ) || count( $this->fields ) == 0 ){
		hiweb()->console()->warn( 'Для формы id[' . $this->id . '] нет полей ввода.', true );
		?>Для формы id[<b><?php echo $this->id ?></b>] нет полей ввода.<?php
	}else
		foreach( $this->get_fields() as $id => $field ){
			if( $field instanceof hw_input ){
				?>
				<div class="hw-form-field">
				<p><strong><?php echo $field->title() ?></strong></p>
				<?php $field->the(); ?>
				<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
				</div><?php
			}else{
				?>
				<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
			}
		}

?>