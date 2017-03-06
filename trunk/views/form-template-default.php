<?php

	/** @var hw_form $this */

	foreach( $this->get_fields() as $field ){
		if( $field instanceof hw_field ){
			?>
			<div class="hw-form-field hw-field-<?php echo $field->get_type() ?>">
			<p><strong><?php echo $field->name() ?></strong></p>
			<?php $field->the(); ?>
			<?php echo $field->description() != '' ? '<p class="description">' . $field->description() . '</p>' : ''; ?>
			</div><?php
		} else {
			?>
			<div class="hw-form-field"><?php hiweb()->dump( $field ) ?></div><?php
		}
	}

?>