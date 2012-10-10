<?php
/**
 * Link
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * A link shared on a user's wall. The User, Application, and Page objects have a links connection.
 * Requires "read_stream" permissions.
 *
 * @link https://developers.facebook.com/docs/reference/api/link/
 * @package Facebook
 */
class Link extends \Sledgehammer\GraphObject {

	/**
	 * The link ID.
	 * @var string
	 */
	public $id;

	/**
	 * The user that created the link.
	 *
	 * Object containing the id and name field
	 */
	public $from;

	/**
	 * The URL that was shared.
	 *
	 * string containing a valid URL
	 */
	public $link;

	/**
	 * The name of the link.
	 * @var string
	 */
	public $name;

	/**
	 * All of the comments on this link.
	 *
	 * Array of objects containing id, from, message and created_time fields
	 */
	public $comments;

	/**
	 * A description of the link (appears beneath the link caption).
	 * @var string
	 */
	public $description;

	/**
	 * A URL to the link icon that Facebook displays in the news feed.
	 *
	 * string containing a valid URL
	 */
	public $icon;

	/**
	 * A URL to the thumbnail image used in the link post.
	 *
	 * string containing a valid URL
	 */
	public $picture;

	/**
	 * The optional message from the user about this link.
	 * @var string
	 */
	public $message;

	/**
	 * The time the message was published.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $created_time;

	/**
	 * The type of this object; always returns link.
	 * @var string
	 */
	public $type;

	/**
	 * All of the comments on this link.
	 *
	 * Returns array of objects containing id, from, message and created_time fields.
	 * @var Collection|GraphObject
	 */
//	public $comments;

	/**
	 * Users who like this link.
	 *
	 * Returns array of objects containing the id and name fields.
	 * @var Collection|GraphObject
	 */
	public $likes;

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'comments' => array(),
			'likes' => array(),
		);
		return $connections;
	}

}

?>