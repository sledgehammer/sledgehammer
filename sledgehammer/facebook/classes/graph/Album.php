<?php
/**
 * Album
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * An album of photos as represented in the Graph API.
 * Requires "user_photos" or "friend_photos" permissions.
 *
 * @link https://developers.facebook.com/docs/reference/api/album/
 * @package Facebook
 */
class Album extends \Sledgehammer\GraphObject {

	/**
	 * The album ID.
	 * @var string
	 */
	public $id;

	/**
	 * The profile that created this album.
	 *
	 * object containing id and name fields
	 */
	public $from;

	/**
	 * The title of the album.
	 * @var string
	 */
	public $name;

	/**
	 * The description of the album.
	 * @var string
	 */
	public $description;

	/**
	 * The location of the album.
	 * @var string
	 */
	public $location;

	/**
	 * A link to this album on Facebook.
	 *
	 * string containing a valid URL
	 */
	public $link;

	/**
	 * The album cover photo ID.
	 * @var string
	 */
	public $cover_photo;

	/**
	 * The privacy settings for the album.
	 * @var string
	 */
	public $privacy;

	/**
	 * The number of photos in this album.
	 * @var string
	 */
	public $count;

	/**
	 * The type of the album: profile, mobile, wall, normal or album.
	 * @var string
	 */
	public $type;

	/**
	 * The time the photo album was initially created.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $created_time;

	/**
	 * The last time the photo album was updated.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $updated_time;

	/**
	 * Determines whether the UID can upload to the album and returns true if the user owns the album, the album is not full, and the app can add photos to the album.
	 *
	 * boolean
	 */
	public $can_upload;

	/**
	 * The photos contained in this album.
	 * @var Collection|Photo
	 */
	public $photos;

	/**
	 * The likes made on this album.
	 *
	 * Returns array of objects containing id and name fields.
	 * @var Collection|GraphObject
	 */
	public $likes;

	/**
	 * The comments made on this album.
	 *
	 * Returns array of objects containing id, from, message and created_time fields.
	 * @var Collection|GraphObject
	 */
	public $comments;

	/**
	 * The album's cover photo, the first picture uploaded to an album becomes the cover photo for the album.
	 *
	 * Returns HTTP 302 redirect to URL of the album's cover picture
	 * @var Collection|GraphObject
	 */
	public $picture;

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'photos' => array('class' => '\Sledgehammer\Facebook\Photo'),
			'likes' => array(),
			'comments' => array(),
			'picture' => array(),
		);
		return $connections;
	}

}

?>