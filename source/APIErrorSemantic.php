<?

namespace website\api;

/*|
| | [ Application Error Reporting ]
| | 
| | Returns the error semantic for API error responses
| | $_constraints are defined in isomorph Entity.php
|*/

class APIErrorSemantic {

	private static $_constraints  = [

		/*|
		| | [ Entity Class Constraints ]
		|*/

		# (bool) Valid date. Requires (D-M-Y) / 02-10-2002 for EU time
		'date' => array(
			'message' => 'Invalid date',
			'hint' => 'EU time. Requires (D-M-Y). E.g. 02-10-2002',
			'code' => '0007'
		),
		# (bool) Valid datetime. Same as `date`, but with time. (10-02-2000 21:00:00)
		'datetime' => array(
			'message' => 'Invalid datetime',
			'hint' => 'Date and time should resemble [D-M-Y H:M:S] (10-02-2000 21:00:00)',
			'code' => '0008'
		),
		# (bool) Email validation
		'email' => array(
			'message' => 'Invalid email',
			'hint' => 'The email supplied is invalid and cannot be used',
			'code' => '0009'
		),
		# (bool) Is file. Instance of (\core\data\File)
		'file' => array(
			'message' => 'File exception',
			'hint' => 'Upload must be an instance of the File class',
			'code' => '0010'
		),
		# (int) Specify maximun file size
		'image-sizes' => array(
			'message' => 'File size exceeded',
			'hint' => 'File is too large',
			'code' => '0011'
		),
		# (class) Instance of specified class
		'instance-of' => array(
			'message' => 'Invalid instance',
			'hint' => 'Check the accepted instance of this column in the Entity class',
			'code' => '0012'
		),
		# (int) Max integer length 
		'max' => array(
			'message' => 'Integer size exceeded',
			'hint' => 'The maximum integer size has been exceeded',
			'code' => '0013'
		),
		# (string) Max string length 
		'maxlength' => array(
			'message' => 'String size exceeded',
			'hint' => 'The maximum string size has been exceeded',
			'code' => '0014'
		),
		# (int) Min integer length
		'min' => array(
			'message' => 'Integer size too small',
			'hint' => 'The integer size is below the minimum',
			'code' => '0015'
		),
		# (string) Min string length
		'minlength' => array(
			'message' => 'The string length is below the minimum',
			'hint' => 'The string length is below the minimum',
			'code' => '0016'
		),
		# (bool) Required field
		'required' => array(
			'message' => 'Is required',
			'hint' => 'This field is required. No value was supplied',
			'code' => '0017'
		),
		# (bool) DB Unique
		'unique' => array(
			'message' => 'Not unique',
			'hint' => 'The database already contains this value. Must be unique',
			'code' => '0018'
		),
		# (bool) is URL. Requires http:// or https://
		'url' => array(
			'message' => 'Invalid URL',
			'hint' => 'URL Requires http://',
			'code' => '0019'
		),

		/*|
		| | [ User Module Create Account Constraints ]
		|*/

		# (String) Passwords match. Returns `confirm` if false
		'confirm' => array(
			'message' => 'Not matching',
			'hint' => 'The supplied fields fields do not match',
			'code' => '0023'
		),

		# (String) Passwords match. Returns `confirm` if false
		'authenticate' => array(
			'message' => 'Authentication failed',
			'hint' => 'The user was not authenticated',
			'code' => '0024'
		),

		/*|
		| | [ Subscription Module Constraints ]
		|*/

		# (String) Passwords match. Returns `confirm` if false
		'date_offset' => array(
			'message' => 'Date not met',
			'hint' => 'The date supplied was below the minimum offset',
			'code' => '0025'
		),
	];

	/**
	 * Parse validation exception into error semantic
	 *
	 * @param array $errors
	 *
	 * @return []
	 */
	public static function parseValidationException(array $errors = null) {

		$errorConstruct = array();

		foreach ($errors as $key => $value) {

			$currentError = array();

			if (array_key_exists($value, self::$_constraints) === true) {
				$currentError['message'] = self::$_constraints[$value]['message'];
				$currentError['hint'] = self::$_constraints[$value]['hint'];
				$currentError['code'] = self::$_constraints[$value]['code'];
				$currentError['context'] = $key;
			}
			else{
				$currentError['message'] = 'Undefined validation error';
				$currentError['hint'] = 'See defined constraints';
				$currentError['code'] = '0002';
				$currentError['context'] = $key;
			}

			$errorConstruct[] = $currentError;
		}

		// return (!empty($errorConstruct)) ? $errorConstruct : null;
		return (!empty($errorConstruct)) ? $errorConstruct : [];
	}


	/**
	 * Create custom error
	 *
	 * @return []
	 */
	public static function overrideError($message, $hint, $code = null, $context) {

		$errorConstruct = array(
			'message' => $message,
			'hint' => $hint,
			'code' => ($code === null) ? 'N/A' : $code,
			'context' => $context,
		);

		return $errorConstruct;
	}


	/**
	 * Throw unexpected exception
	 *
	 * @return []
	 */
	public static function unexpectedException() {

		$errorConstruct = array(
			'message' => 'System error',
			'hint' => 'Unexpected System Exception. Operational logic did not perform as expected',
			'code' => '0020',
			'context' => 'system',
		);

		return $errorConstruct;
	}


	/**
	 * Throw busy exception (CRON)
	 *
	 * @return []
	 */
	public static function busyException() {

		$errorConstruct = array(
			'message' => 'Traffic exception',
			'hint' => 'The system is currently busy. Please try in another minute',
			'code' => '0027',
			'context' => 'system',
		);

		return $errorConstruct;
	}


	/**
	 * Throw not found exception
	 *
	 * @return []
	 */
	public static function notFound() {

		$errorConstruct = array(
			'message' => 'Not found',
			'hint' => 'The requested entity was not found.',
			'code' => '0021',
			'context' => 'system',
		);

		return $errorConstruct;
	}
}
