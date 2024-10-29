<?php
/**
 * @since 1.0.0
 * @author Stefan Boonstra
 */
class DeveloperToolsAdmin
{
	/**
	 * @since 1.0.0
	 */
	static function init()
	{
		add_action('admin_menu', array(__CLASS__, 'addMenuItems'));
	}

	/**
	 * @since 1.0.0
	 */
	static function addMenuItems()
	{
		add_menu_page(
			__('Developer Tools', 'developer-tools'),
			__('Developer Tools', 'developer-tools'),
			'manage_options',
			'developer-tools',
			array(__CLASS__, 'renderDeveloperToolsPage'),
			'dashicons-media-code'
		);

		add_submenu_page(
			'developer-tools',
			__('Log', 'developer-tools'),
			__('Log', 'developer-tools'),
			'manage_options',
			'developer-tools-logger',
			array(__CLASS__, 'renderLogPage')
		);
	}

	/**
	 * @since 1.0.0
	 */
	static function renderDeveloperToolsPage()
	{
		DeveloperTools::outputView('admin-info.php');
	}

	/**
	 * @since 1.0.0
	 */
	static function renderLogPage()
	{
		DeveloperTools::outputView('admin-log.php');
	}
}