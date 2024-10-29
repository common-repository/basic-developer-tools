<?php
/**
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class DeveloperToolsLogger
{
	/** @var int */
	static $logID;

	/** @var string */
	static $logOptionName = 'developer-tools-logger-log';

	/**
	 * @since 1.0.0
	 */
	static function init()
	{
		// Empty method is called to auto load class
	}

	/**
	 * Logs any type of input.
	 *
	 * @since 1.0.0
	 */
	static function log()
	{
		$input = func_get_args();

		if (count($input) === 1)
		{
			$input = $input[0];
		}

		ob_start();
		var_dump($input);
		$content = htmlspecialchars(ob_get_clean());

		ob_start();
		debug_print_backtrace();
		$backtrace = ob_get_clean();

		$entries = self::getAll();

		$entries[] = array(
			'timestamp' => microtime(true),
			'content'   => $content,
			'backtrace' => $backtrace,
		);

		self::update(serialize($entries));
	}

	/**
	 * Returns all log entries from the database.
	 *
	 * @since 1.0.0
	 * @return array $entries
	 */
	static function getAll()
	{
		global $wpdb;

		$row = $wpdb->get_row(
			"SELECT $wpdb->options.option_id, $wpdb->options.option_value " .
			"FROM $wpdb->options " .
			"WHERE $wpdb->options.option_name = '" . self::$logOptionName . "' " .
			"LIMIT 0,1",
			ARRAY_A
		);

		if (!is_array($row))
		{
			return array();
		}

		// Cache option ID
		self::$logID = $row['option_id'];

		$entries = @unserialize($row['option_value']);

		if ($entries !== false &&
			is_array($entries))
		{
			return $entries;
		}

		return array();
	}

	/**
	 * Pops and returns all log entries from the database.
	 *
	 * @since 1.0.0
	 * @return array $entries
	 */
	static function popAll()
	{
		$entries = self::getAll();

		// Clear log
		self::update(null);

		return $entries;
	}

	/**
	 * Echoes the entire log as JSON, using the self::pop method. Kills the application when done.
	 *
	 * @since 1.0.0
	 */
	static function jsonOutputLog()
	{
		echo json_encode(self::popAll());

		die();
	}

	/**
	 * Updates the log to the passed string value.
	 *
	 * @since 1.0.0
	 * @param string $value
	 */
	protected static function update($value)
	{
		global $wpdb;

		// Log ID is not in cache
		if (!is_numeric(self::$logID))
		{
			// Try to get log ID
			$logID = $wpdb->get_var(
				"SELECT $wpdb->options.option_id " .
				"FROM $wpdb->options " .
				"WHERE $wpdb->options.option_name = '" . self::$logOptionName . "' " .
				"LIMIT 0,1"
			);

			// Create if doesn't exist
			if (!is_numeric($logID))
			{
				$wpdb->insert(
					$wpdb->options,
					array(
						'option_name'  => self::$logOptionName,
						'option_value' => $value,
						'autoload'     => 'no',
					),
					array(
						'%s',
						'%s',
						'%s',
					)
				);

				return;
			}

			self::$logID = $logID;
		}

		// Update
		$wpdb->update(
			$wpdb->options,
			array(
				'option_name'  => self::$logOptionName,
				'option_value' => $value,
			),
			array('option_id' => self::$logID),
			array(
				'%s',
				'%s',
			),
			array('%d')
		);
	}
}

if (!function_exists('l'))
{
	/**
	 * Shorthand function for the DeveloperToolsLogger::Log method
	 *
	 * @since 1.0.0
	 */
	function l()
	{
		call_user_func_array(array('DeveloperToolsLogger', 'log'), func_get_args());
	}
}

if (!function_exists('dtlog'))
{
	/**
	 * Shorthand function for the DeveloperToolsLogger::Log method
	 *
	 * @since 1.0.0
	 */
	function dtlog()
	{
		call_user_func_array(array('DeveloperToolsLogger', 'log'), func_get_args());
	}
}