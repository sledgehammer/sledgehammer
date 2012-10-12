<?php
/**
 * Achievement
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * The achievement(Instance) object represents the achievement achieved by a user for a particular app.
 *
 * @link https://developers.facebook.com/docs/reference/api/achievement/
 * @package Facebook
 */
class Achievement extends \Sledgehammer\GraphObject {

	/**
	 * id of the achievement(instance).
	 * @var string
	 */
	public $id;

	/**
	 * The user who achieved the achievement.
	 *
	 * object containing the id and name of user
	 */
	public $from;

	/**
	 * Time at which the achievement was achieved.
	 *
	 * string containing an ISO-8601 datetime
	 */
	public $created_time;

	/**
	 * The application in which the user achieved the achievement.
	 *
	 * object containing id and name of application
	 */
	public $application;

	/**
	 * The achievement object that the user achieved.
	 *
	 * object containing the id, url, type, and title of the achievement
	 */
	public $achievement;

	/**
	 * likes received by the story.
	 *
	 * object containing the count of likes, as well as an array containing the name and id of users who like it
	 */
	public $likes;

	/**
	 * Comments received by the achievement story.
	 *
	 * object containing the count of comments, as well as an array containing id of the comment, from object containing the name and id of users who created the comment, message and created_time of the comment
	 */
	public $comments;

}

?>