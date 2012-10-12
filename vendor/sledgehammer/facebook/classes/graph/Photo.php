<?php
/**
 * FacebookPhoto
 */
namespace Sledgehammer\Facebook;
/**
 * An individual photo as represented in the Graph API.
 *
 * @package Facebook
 */
class Photo extends \Sledgehammer\GraphObject {

	/**
	 * The photo ID.
	 * @var string
	 */
	public $id;

	/**
	 * The profile (user or page) that posted this photo.
	 *
	 * object containing id and name fields
	 */
	public $from;

	/**
	 * The user provided caption given to this photo - do not include advertising in this field.
	 * @var string
	 */
	public $name;

	/**
	 * The icon that Facebook displays when photos are published to the Feed.
	 *
	 * string representing a valid URL
	 */
	public $icon;

	/**
	 * The thumbnail-sized source of the photo.
	 *
	 * string representing a valid URL
	 */
	public $picture;

	/**
	 * The source image of the photo - currently this can have a maximum width or height of 720px, increasing to 960px on 1st March 2012.
	 *
	 * string representing a valid URL
	 */
	public $source;

	/**
	 * The height of the photo in pixels.
	 * @var number
	 */
	public $height;

	/**
	 * The width of the photo in pixels.
	 * @var number
	 */
	public $width;

	/**
	 * The 4 different stored representations of the photo.
	 *
	 * array of objects, containing height, width, and source fields
	 */
	public $images;

	/**
	 * A link to the photo on Facebook.
	 *
	 * string representing a valid URL
	 */
	public $link;

	/**
	 * Location associated with a Photo, if any.
	 *
	 * object containing id and name of Page associated with this location, and a location field containing geographic information such as latitude, longitude, country, and other fields (fields will vary based on geography and availability of information)
	 */
	public $place;

	/**
	 * The time the photo was initially published.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $created_time;

	/**
	 * The last time the photo or its caption was updated.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $updated_time;

	/**
	 * The position of this photo in the album.
	 * @var number
	 */
	public $position;

	/**
	 * All of the comments on this photo..
	 *
	 * array of objects containing id, from, message and created_time fields.
	 * @var Collection|GraphObject
	 */
	public $comments;

	/**
	 * Users who like this photo..
	 *
	 * array of objects containing the id and name fields.
	 * @var Collection|GraphObject
	 */
	public $likes;

	/**
	 * The Users tagged in the photo..
	 *
	 * Tags with names and IDs (if available).
	 * @var Collection|GraphObject
	 */
	public $tags;

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'comments' => array(),
			'likes' => array(),
			'picture' => array(),
			'tags' => array(),
		);
		return $connections;
	}

}

?>