<?

namespace website\packages;

/*|
| | [ Application Control ]
| | 
| | Contains methods for reoccurring operations and API wide functionality
| |
|*/

class ApplicationControl {
	
	/**
	 * Get raw input data and decode json
	 *
	 * @return json
	 */
	public function jsonValues() {

		return json_decode( file_get_contents( 'php://input' ), true );
	}

	/**
	 * Display a generic error response
	 *
	 * @return json
	 */
	public function responseError($error = null) {

		return json_encode(array(
			'error' => !empty($error) ? $error : 'There was an issue with the request'
		));
	}
}
