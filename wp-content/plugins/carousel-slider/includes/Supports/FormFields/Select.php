<?php

namespace CarouselSlider\Supports\FormFields;

use CarouselSlider\Supports\Validate;

/**
 * Select class
 */
class Select extends BaseField {

	/**
	 * Render field html
	 *
	 * @inerhitDoc
	 */
	public function render(): string {
		$choices = $this->get_setting( 'choices' );
		if ( Validate::checked( $this->get_setting( 'searchable' ) ) ) {
			$this->set_setting( 'field_class', $this->get_setting( 'field_class' ) . ' select2' );
		}
		$this->set_setting( 'type', 'select' );

		$is_multiple = $this->get_setting( 'multiple' );
		$value       = $this->get_value();
		if ( $is_multiple && is_string( $value ) ) {
			$value = explode( ',', wp_strip_all_tags( rtrim( $value, ',' ) ) );
		}
		$html = '<select ' . $this->build_attributes() . '>';
		foreach ( $choices as $key => $label ) {
			if ( $is_multiple ) {
				$selected = in_array( $key, $value ) ? 'selected' : ''; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			} else {
				$selected = $this->get_value() === $key ? 'selected' : '';
			}
			$html .= '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $label ) . '</option>';
		}
		$html .= '</select>';

		return $html;
	}
}
