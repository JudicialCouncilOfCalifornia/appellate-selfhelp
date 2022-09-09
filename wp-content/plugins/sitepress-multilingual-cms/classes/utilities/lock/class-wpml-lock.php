<?php

namespace WPML\Utilities;

class Lock implements ILock {

	/** @var \wpdb  */
	private $wpdb;

	/** @var string  */
	private $name;

	/**
	 * Lock constructor.
	 *
	 * @param \wpdb $wpdb
	 * @param string $name
	 */
	public function __construct( \wpdb $wpdb, $name ) {
		$this->wpdb = $wpdb;
		$this->name = 'wpml.' . $name;
	}

	/**
	 * Creates a lock using WordPress options ( Based on WP class WP_Upgrader ).
	 *
	 * @param int    $release_timeout Optional. The duration in seconds to respect an existing lock.
	 *                                Default: 1 hour.
	 * @return bool False if a lock couldn't be created or if the lock is still valid. True otherwise.
	 */
	public function create( $release_timeout = null ) {
		if ( ! $release_timeout ) {
			$release_timeout = HOUR_IN_SECONDS;
		}
		$lock_option = $this->name . '.lock';

		// Try to lock.
		$lock_result = $this->wpdb->query( $this->wpdb->prepare( "INSERT IGNORE INTO {$this->wpdb->options} ( `option_name`, `option_value`, `autoload` ) VALUES (%s, %s, 'no') /* LOCK */", $lock_option, time() ) );

		if ( ! $lock_result ) {
			$lock_result = get_option( $lock_option );

			// If a lock couldn't be created, and there isn't a lock, bail.
			if ( ! $lock_result ) {
				return false;
			}

			// Check to see if the lock is still valid. If it is, bail.
			if ( $lock_result > ( time() - $release_timeout ) ) {
				return false;
			}

			// There must exist an expired lock, clear it and re-gain it.
			$this->release();

			return $this->create( $release_timeout );
		}

		// Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
		update_option( $lock_option, time() );

		return true;
	}

	/**
	 * Releases an upgrader lock.
	 *
	 * @return bool True if the lock was successfully released. False on failure.
	 */
	public function release() {
		return delete_option( $this->name . '.lock' );
	}

}
