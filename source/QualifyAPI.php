<?php

namespace website\api;

use website\shop\data\Order;

interface QualifyAPI {

	/**
	 * Return viewable columns. Should not return database sensitive information.
	 *
	 * @return (object User)
	 */
	public function returnViewableColumns();

	/**
	 * Filters the POST request to contain only client updatable columns. Everything else is unset.
	 * Values used in the parameter should be validated first.
	 *
	 * @param array $post
	 *
	 * @return array
	 */
	public function filterClientUpdatableColumns(array $post);


	/**
	 * Static method. Filters the POST request to contain only client creatable columns. Everything else is unset.
	 * Values used in the parameter should be validated first.
	 *
	 * @param array $post
	 *
	 * @return array
	 */
	public static function filterClientCreatableColumns(array $post);
}
