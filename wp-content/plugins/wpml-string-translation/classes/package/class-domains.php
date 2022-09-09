<?php

namespace WPML\ST\Package;

class Domains {

	/** @var \wpdb $wpdb */
	private $wpdb;

	/** @var array $domains */
	private $domains;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param string|null $domain
	 *
	 * @return bool
	 */
	public function isPackage( $domain ) {
		return $domain && in_array( $domain, $this->getDomains(), true );
	}

	/**
	 * @see \WPML_Package::get_string_context_from_package for how the package domain is built
	 *
	 * @return array
	 */
	private function getDomains() {
		if ( ! $this->domains ) {
			$this->domains = $this->wpdb->get_col(
				"SELECT CONCAT(kind_slug, '-', name) FROM {$this->wpdb->prefix}icl_string_packages"
			);
		}

		return $this->domains;
	}
}
