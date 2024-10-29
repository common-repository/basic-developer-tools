<?php
/**
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class DeveloperToolsException
{
	/**
	 * @since 1.0.0
	 */
	static function init()
	{
		set_error_handler(array(__CLASS__, 'errorHandler'));
		set_exception_handler(array(__CLASS__, 'exceptionHandler'));
	}

	/**
	 * @param int    $errorNumber
	 * @param string $errorString
	 * @param string $errorFile
	 * @param int    $errorLine
	 * @param array  $errorContext
	 */
	static function errorHandler($errorNumber, $errorString, $errorFile = '', $errorLine = -1, $errorContext = array())
	{
		DeveloperToolsLogger::log($errorNumber, $errorString, $errorFile, $errorLine, $errorContext);
	}

	/**
	 * @param Exception $exception
	 */
	static function exceptionHandler($exception)
	{
		DeveloperToolsLogger::log($exception->getMessage());
	}
}