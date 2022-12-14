<div class="modal-header">
	<div class="modal-header__title add-field">
		<i :class="modal.data.icon"></i>
		<h4>{{ modal.data.name }} Field</h4>
	</div>
</div>
<div class="modal-body">
	<?php
	$fields = \cBuilder\Helpers\CCBFieldsHelper::fields();
	?>

	<?php foreach ( $fields as $key => $field ) : ?>
		<component
				inline-template
				:id="getEditID"
				:index="getIndex"
				:order="getOrderId"
				:field="builderData"
				@save="addToBuilder"
				@cancel="closeModal"
				:available="$store.getters.getBuilder"
				is="<?php echo esc_attr( $field['type'] ); ?>-field"
				v-if="getType === '<?php echo esc_attr( $field['type'] ); ?>'"
		>
			<?php echo \cBuilder\Classes\CCBTemplate::load( '/admin/fields/' . $field['type'] . '-field' ); //phpcs:ignore ?>
		</component>

	<?php endforeach; ?>
</div>
