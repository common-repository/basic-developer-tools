<?php
/*
 Plugin Name: Basic Developer Tools
 Plugin URI: http://wordpress.org/extend/plugins/basic-developer-tools/
 Description: Basic Developer Tools helps developers to log any variable, error or exception of which they need the value, without bloating their live environment.
 Version: 1.0.2
 Requires at least: 3.5
 Author: StefanBoonstra
 Author URI: http://stefanboonstra.com/
 License: GPLv2
 Text Domain: developer-tools
*/

/**
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class DeveloperTools
{
	/** @var string $version */
	static $version = '1.0.2';

	/**
	 * Bootstraps the application by assigning the right functions to
	 * the right action hooks.
	 *
	 * @since 1.0.0
	 */
	static function bootstrap()
	{
		self::autoInclude();

		// Initialize localization on init
		add_action('init', array(__CLASS__, 'localize'));

		// Include backend scripts and styles
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueBackendScripts'));

		DeveloperToolsAdmin::init();
		DeveloperToolsLogger::init();
		DeveloperToolsException::init();

		// Log on logging hook
		add_action('dtlog', array('DeveloperToolsLogger', 'log'));

		// Hook AJAX methods
		add_action('wp_ajax_developer_tools_pop_log'       , array('DeveloperToolsLogger', 'jsonOutputLog'));
		add_action('wp_ajax_nopriv_developer_tools_pop_log', array('DeveloperToolsLogger', 'jsonOutputLog'));
	}

	/**
	 * Includes backend script.
	 *
	 * Should always be called on the admin_enqueue_scrips hook.
	 *
	 * @since 1.0.0
	 */
	static function enqueueBackendScripts()
	{
		// Function get_current_screen() should be defined, as this method is expected to fire at 'admin_enqueue_scripts'
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		wp_enqueue_script(
			'developer-tools-backend-script',
			self::getPluginUrl() . '/js/min/all.backend.min.js',
			array('jquery'),
			self::$version
		);

		wp_enqueue_style(
			'developer-tools-backend-style',
			self::getPluginUrl() . '/css/all.backend.css',
			array(),
			self::$version
		);
	}

	/**
	 * Translates the plugin
	 *
	 * @since 1.0.0
	 */
	static function localize()
	{
		load_plugin_textdomain(
			'developer-tools',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages/'
		);
	}

	/**
	 * Returns url to the base directory of this plugin.
	 *
	 * @since 1.0.0
	 * @return string pluginUrl
	 */
	static function getPluginUrl()
	{
		return plugins_url('', __FILE__);
	}

	/**
	 * Returns path to the base directory of this plugin
	 *
	 * @since 1.0.0
	 * @return string pluginPath
	 */
	static function getPluginPath()
	{
		return dirname(__FILE__);
	}

	/**
	 * Outputs the passed view. It's good practice to pass an object like an stdClass to the $data variable, as it can
	 * be easily checked for validity in the view itself using "instanceof".
	 *
	 * @since 1.0.0
	 * @param string   $view
	 * @param stdClass $data (Optional, defaults to null)
	 */
	static function outputView($view, $data = null)
	{
		include self::getPluginPath() . '/views/' . $view;
	}

	/**
	 * Uses self::outputView to render the passed view. Returns the rendered view instead of outputting it.
	 *
	 * @since 1.0.0
	 * @param string   $view
	 * @param stdClass $data (Optional, defaults to null)
	 * @return string
	 */
	static function getView($view, $data = null)
	{
		ob_start();

		self::outputView($view, $data);

		return ob_get_clean();
	}

	/**
	 * This function will load classes automatically on-call.
	 *
	 * @since 1.0.0
	 */
	static function autoInclude()
	{
		if (!function_exists('spl_autoload_register'))
		{
			return;
		}

		function developerToolsAutoLoader($name)
		{
			$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';

			if (is_file($file))
			{
				require_once $file;
			}
		}

		spl_autoload_register('developerToolsAutoLoader');
	}
}

/**
 * Activate plugin
 */
DeveloperTools::bootstrap();
