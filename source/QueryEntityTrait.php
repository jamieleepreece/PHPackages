<?

namespace website\api;

use core\data\SQL;
use Carbon\Carbon;

trait QueryEntityTrait {

    /**
     * Static method. Filters the POST request to contain only client creatable columns. Everything else is unset.
     * Values used in the parameter should be validated first.
     *
     * @param array $post
     *
     * @return array
     */
    public static function generateSystemColumnInformation(array $post) {

        if (isset(self::$_columns['created'])) {
            $post['created'] = Carbon::now();
        }

        return $post;
    }

    /**
     * Static method. Filters the POST request to contain only client creatable columns. Everything else is unset.
     * Values used in the parameter should be validated first.
     *
     * @param array $post
     *
     * @return array
     */
    public static function filterClientCreatableColumns(array $post) {

        foreach ($post as $column => $value) {

            if (!isset(self::$_columns[$column]['api_updatable']) || self::$_columns[$column]['api_updatable'] === false) {

                unset($post[$column]);
            }
        }
        if (isset(self::$_columns['created'])) {
            $post['created'] = Carbon::now();
        }

        return $post;
    }

    /**
     * Filters the POST request to contain only client updatable columns. Everything else is unset.
     * Values used in the parameter should be validated first.
     *
     * @param array $post
     *
     * @return array
     */
    public function filterClientUpdatableColumns(array $post) {

        foreach ($post as $key => $value) {

            $boolVal = $this->column($key, 'api_updatable');

            if ($boolVal !== true) {
                unset($post[$key]);
            }
        }

        return $post;
    }

    /**
     * Return viewable columns. Should not return database sensitive information.
     *
     * @return (object User)
     */
    public function returnViewableColumns() {

        foreach (static::$_columns as $column => $options) {

            $boolVal = $this->column($column, 'api_viewable');

            if ($boolVal !== true) {
                unset($this->{$column});
            }
        }

        return $this;
    }
}