<?php

namespace App\Conveniences;

use Silex\Application;

class Permissions
{
	public function __invoke(Application $app, $required_level, $permitted_ids = null): bool
	{
		return $this->check($app, $required_level, $permitted_ids);
	}

	public function check(Application $app, $required_level, $permitted_ids = null): bool
	{
		$session = $app['session'];
		$user_level = $session->get('permission_level');
		$permission_addl_json = $session->get('permission_addl_json');
		$super_admin = $session->get('super');

		if($permitted_ids && isset($permitted_ids['service_level']) && !$super_admin)
		{
			if(defined('SERVICE_LEVEL') && SERVICE_LEVEL != $permitted_ids['service_level']) $app->abort(403, 'This feature is only available in our Pro-Plus plan. Please contact your HomeTown Account Manager for details.');
		}
		if($permitted_ids && $permission_addl_json)
		{
			if(isset($permitted_ids['event_id'], $permission_addl_json['events']))
			{
				$eventid = $permitted_ids['event_id'];
				$match = false;
				foreach($permission_addl_json['events'] as $events)
				{
					if($eventid == $events)
					{
						$match = true;
						break;
					}
				}
				if(!$match) $app->abort(403, 'Permission denied to this resource');
			}
			if(isset($permitted_ids['venue_id'], $permission_addl_json['venues']))
			{
				$venueid = $permitted_ids['venue_id'];
				$match = false;
				foreach($permission_addl_json['venues'] as $venues)
				{
					if($venueid == $venues)
					{
						$match = true;
						break;
					}
				}
				if(!$match) $app->abort(403, 'Permission denied to this resource');
			}
			if(isset($permitted_ids['school_id'], $permission_addl_json['schools']))
			{
				$schoolid = $permitted_ids['school_id'];
				$match = false;
				foreach($permission_addl_json['schools'] as $schools)
				{
					if($schoolid == $schools)
					{
						$match = true;
						break;
					}
				}
				if(!$match) $app->abort(403, 'Permission denied to this resource');
			}
			if(isset($permitted_ids['department_id'], $permission_addl_json['departments']))
			{
				$departmentid = $permitted_ids['department_id'];
				$match = false;
				foreach($permission_addl_json['departments'] as $departments)
				{
					if($departmentid == $departments)
					{
						$match = true;
						break;
					}
				}
				if(!$match) $app->abort(403, 'Permission denied to this resource');
			}
		}

		if($user_level < $required_level) $app->abort(403, 'Permission denied to this resource');
		return true;
	}
}
