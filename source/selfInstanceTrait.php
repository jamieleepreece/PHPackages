<?

namespace website\api;

trait selfInstanceTrait {

    /**
     * This class is based on the Singleton design principle, and is meant to allow only a single instance to exist
     */

    private static $instance;

    /**
     * Returns an instance of the Entity class
     *
     * @return (object Class)
     */
    public static function getInstance() {

        if (empty(self::$instance)) {

            self::$instance = new self();
        }

        return self::$instance;
    }
}
