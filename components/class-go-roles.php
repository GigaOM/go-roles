<?php

class GO_Roles
{
	// local variable to cache the extended roles
	private $custom_roles;

	public function __construct()
	{
		global $wpdb;

		// run the filter late (11) so that we add on to anything else that is hooked
		add_filter( 'option_' . $wpdb->prefix . 'user_roles', array( $this, 'option_user_roles' ), 11 );
	}// end __construct

	/**
	 * Hooked to the option_user_roles filter
	 *
	 * @param array $base_roles default to array, WordPress roles array
	 */
	public function option_user_roles( $base_roles = array() )
	{
		// check a local object variable for performance
		if ( ! $this->custom_roles )
		{
			$base_roles = $base_roles ?: array();
			$this->custom_roles = $this->extend_roles( $base_roles );
		}// end if

		return $this->custom_roles;
	}//end option_user_roles

	/**
	 * add custom roles to the baseline WordPress roles
	 *
	 * @param array $base_roles baseline WordPress roles array
	 * @return array $base_roles
	 */
	private function extend_roles( $base_roles )
	{
		$custom_roles = apply_filters( 'go_roles', array() );
		if ( ! $custom_roles )
		{
			return $base_roles;
		}// end if

		$roles = array_merge( $base_roles, $custom_roles );

		foreach ( $roles as $slug => &$role )
		{
			// Set caps
			if ( isset( $role['capabilities'] ) && is_array( $role['capabilities'] ) )
			{
				$caps = array_keys( $role['capabilities'] );
			}//end if
			else
			{
				$caps = array();
			}// end else

			// Add slug as cap
			$caps[] = $slug;

			// Get caps from any parent roles
			// Note: order in the config file matters.
			// If a role extends another role, the parent role should be defined first so it's capabilities are already processed.
			if ( isset( $role['extends'] ) )
			{
				$role['extends'] = is_array( $role['extends'] ) ? $role['extends'] : array( $role['extends'] );

				foreach ( $role['extends'] as $parent_slug )
				{
					$caps = array_merge( $caps, array_keys( $roles[ $parent_slug ]['capabilities'] ) );
				}// end foreach

				unset( $role['extends'] );
			}// end if

			// Add specific caps
			if ( isset( $role['add_caps'] ) )
			{
				$role['add_caps'] = is_array( $role['add_caps'] ) ? $role['add_caps'] : array( $role['add_caps'] );
				$caps = array_merge( $caps, $role['add_caps'] );
				unset( $role['add_caps'] );
			}// end if

			// Remove specific caps
			if ( isset( $role['remove_caps'] ) )
			{
				$role['remove_caps'] = is_array( $role['remove_caps'] ) ? $role['remove_caps'] : array( $role['remove_caps'] );
				$caps = array_diff( $caps, $role['remove_caps'] );
				unset( $role['remove_caps'] );
			}// end if

			$role['capabilities'] = array_fill_keys( $caps, TRUE );

			$roles[ $slug ] = $role;
		}// end foreach

		// because not sorting it makes it very annoying when shown in the Admin UI.
		// why reversed? no clue, but it gets displayed reversed in the dashboard, so we'll reverse the reversal.
		krsort( $roles );

		return $roles;
	}//end extend_roles
 }//end class

/**
 * Singleton
 *
 * @global GO_Roles $go_roles
 * @return GO_Roles
 */
function go_roles()
{
	global $go_roles;

	if ( ! $go_roles )
	{
		$go_roles = new GO_Roles;
	}//end if

	return $go_roles;
}//end go_roles
