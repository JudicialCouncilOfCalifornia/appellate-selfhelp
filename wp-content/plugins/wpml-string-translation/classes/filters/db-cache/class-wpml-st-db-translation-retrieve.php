<?php

use WPML\Utilities\NullLock;
use WPML\Utilities\ILock;

class WPML_ST_DB_Translation_Retrieve {

	/**
	 * WP DB instance.
	 *
	 * @var wpdb
	 */
	public $wpdb;

	/**
	 * WPML_ST_Gettext_Filters_Activation instance.
	 *
	 * @var WPML_ST_Gettext_Filters_Activation
	 */
	private $gettext_filters_activation;

	/**
	 * @var array
	 */
	protected $loaded = array();

	/**
	 * @var array
	 */
	private $loaded_contexts = array();

	/**
	 * @var WPML_ST_Domain_Fallback
	 */
	private $domain_fallback;

	/**
	 * @var WPML_DB_Chunk
	 */
	private $chunk_retrieve;

	/** @var ILock */
	private $lock;

	/**
	 * WPML_ST_DB_Translation_Retrieve constructor.
	 *
	 * @param wpdb $wpdb WP DB instance.
	 * @param WPML_ST_Gettext_Filters_Activation $gettext_filters_activation WPML_ST_Gettext_Filters_Activation instance.
	 * @paran ILock $lock Lock to stop concurrent requests to load string contexts (optional)
	 */
	public function __construct(
		wpdb $wpdb,
		WPML_ST_Gettext_Filters_Activation $gettext_filters_activation,
		ILock $lock = null
	) {
		$this->wpdb                       = $wpdb;
		$this->gettext_filters_activation = $gettext_filters_activation;
		$this->domain_fallback            = new WPML_ST_Domain_Fallback();
		$this->chunk_retrieve             = new WPML_DB_Chunk( $wpdb );
		$this->lock                       = $lock ?: new NullLock();
	}

	/**
	 * @param string $language
	 * @param string $name
	 * @param string $context
	 * @param string $gettext_context
	 *
	 * @return WPML_ST_Page_Translation|null
	 */
	public function get_translation( $language, $name, $context, $gettext_context = '' ) {
		if ( ! in_array( $context, $this->loaded_contexts ) ) {
			$this->load( $language, $context );
		}

		$translation = $this->try_get_translation( $name, $context, $gettext_context );

		if ( ! $translation && $this->domain_fallback->has_fallback_domain( $context ) ) {
			$context = $this->domain_fallback->get_fallback_domain( $context );

			if ( ! in_array( $context, $this->loaded_contexts ) ) {
				$this->load( $language, $context );
			}
			$translation = $this->try_get_translation( $name, $context, $gettext_context );
		}

		return $translation;
	}

	public function clear_cache() {
		$this->loaded          = array();
		$this->loaded_contexts = array();
	}

	/**
	 * Load translations.
	 *
	 * @param string $language Language.
	 * @param string $context Context.
	 */
	protected function load( $language, $context ) {
		if (
			$this->gettext_filters_activation->should_be_turned_on( $language, $context ) &&
			$this->lock->create()
		) {
			$this->load_from_db( $language, $context );
			$this->lock->release();
		}

		$this->loaded_contexts[] = $context;
	}

	/**
	 * @param string $context
	 *
	 * @return int
	 */
	private function get_number_of_strings_in_context( $context ) {
		$sql = "SELECT COUNT(id) FROM {$this->wpdb->prefix}icl_strings WHERE context = %s";
		$sql = $this->wpdb->prepare( $sql, array( $context ) );

		return (int) $this->wpdb->get_var( $sql );
	}

	/**
	 * @param string $name
	 * @param string $context
	 * @param string $gettext_content
	 *
	 * @return string
	 */
	private function create_key( $name, $context, $gettext_content ) {
		return md5( $context . $name . $gettext_content );
	}

	/**
	 * @param $name
	 * @param $context
	 * @param $gettext_context
	 *
	 * @return null|WPML_ST_Page_Translation
	 */
	private function try_get_translation( $name, $context, $gettext_context ) {
		$key = $this->create_key( $name, $context, $gettext_context );
		if ( isset( $this->loaded[ $context ][ $gettext_context ][ $key ] ) ) {
			$row_data = $this->loaded[ $context ][ $gettext_context ][ $key ];

			return $this->build_translation( $row_data, $name, $context, $gettext_context );
		}

		return null;
	}

	/**
	 * @param array $row_data
	 * @param string $name
	 * @param string $context
	 * @param string $gettext_context
	 *
	 * @return WPML_ST_Page_Translation
	 */
	private function build_translation( array $row_data, $name, $context, $gettext_context ) {
		return new WPML_ST_Page_Translation(
			$row_data[0],
			$name,
			$context,
			$row_data[1],
			count( $row_data ) > 2, // has an original value
			$gettext_context
		);
	}

	/**
	 * @param string $language
	 * @param string $context
	 */
	private function load_from_db( $language, $context ) {
		$args = array( $language, $language, $context );

		$query = "
			SELECT
				s.id,
				st.status,
				s.domain_name_context_md5 AS ctx ,
				st.value AS translated,
				st.mo_string AS mo_string,
				s.value AS original,
				s.gettext_context
			FROM {$this->wpdb->prefix}icl_strings s
			LEFT JOIN {$this->wpdb->prefix}icl_string_translations st
				ON s.id=st.string_id
					AND st.language=%s
					AND s.language!=%s
			WHERE s.context = %s
			";

		$rowset = $this->chunk_retrieve->retrieve( $query, $args, $this->get_number_of_strings_in_context( $context ) );

		foreach ( $rowset as $row_data ) {
			$this->parse_result( $row_data, $context );
		}
	}

	/**
	 * @param array $row_data
	 * @param string $context
	 */
	private function parse_result( array $row_data, $context ) {
		$has_translation = ! empty( $row_data['translated'] ) && ICL_TM_COMPLETE == $row_data['status'];
		if ( $has_translation ) {
			$value = $row_data['translated'];
		} else {
			$use_mo_string = ! empty( $row_data['mo_string'] );
			if ( $use_mo_string ) {
				$value = $row_data['mo_string'];
			} else {
				$value = $row_data['original'];
			}
		}

		$data = array( $row_data['id'], $value );
		if ( $has_translation || $use_mo_string ) {
			$data[] = $row_data['original'];
		}

		$this->loaded[ $context ][ $row_data['gettext_context'] ][ $row_data['ctx'] ] = $data;
	}
}
