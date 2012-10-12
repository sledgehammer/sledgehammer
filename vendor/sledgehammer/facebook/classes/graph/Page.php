<?php
/**
 * Page
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * A Page in the Graph API.
 *
 * @link https://developers.facebook.com/docs/reference/api/page/
 * @package Facebook
 */
class Page extends \Sledgehammer\GraphObject {

	/**
	 * The Page's ID.
	 * @var string
	 */
	public $id;

	/**
	 * The Page's name.
	 * @var string
	 */
	public $name;

	/**
	 * Link to the page on Facebook.
	 *
	 * string containing a valid URL
	 */
	public $link;

	/**
	 * The Page's category.
	 * @var string
	 */
	public $category;

	/**
	 * Indicates whether the page is published and visible to non-admins.
	 * @var boolean
	 */
	public $is_published;

	/**
	 * Indicates whether the current session user can post on this Page.
	 * @var boolean
	 */
	public $can_post;

	/**
	 * The number of users who like the Page.
	 * @var number
	 */
	public $likes;

	/**
	 * The Page's street address, latitude, and longitude (when available).
	 *
	 * dictionary
	 */
	public $location;

	/**
	 * The phone number (not always normalized for country code) for the Page.
	 * @var string
	 */
	public $phone;

	/**
	 * The total number of users who have checked in to the Page.
	 * @var number
	 */
	public $checkins;

	/**
	 * Link to the Page's profile picture.
	 * @var string
	 */
	public $picture;

	/**
	 * The JSON object including cover_id (the ID of the photo), source (the URL for the cover photo), and offset_y (the percentage offset from top [0-100]).
	 *
	 * JSON object
	 */
	public $cover;

	/**
	 * Link to the external website for the page.
	 * @var string
	 */
	public $website;

	/**
	 * The number of people that are talking about this page (last seven days).
	 * @var number
	 */
	public $talking_about_count;

	/**
	 * A Page admin access_token for this page; The current user must be an administrator of this page; only returned if specifically requested via the fields URL parameter.
	 * Requires "manage_pages" permissions.
	 * @var string
	 */
	public $access_token;

	/**
	 * The Page's wall.
	 *
	 * Returns array of Post objects.
	 * @var Collection|GraphObject
	 */
	public $feed;

	/**
	 * The Page's profile picture.
	 *
	 * Returns Returns a HTTP 302 with the URL of the user's profile picture.
	 * @var Collection|GraphObject
	 */
//	public $picture;

	/**
	 * The settings for this page.
	 *
	 * Returns array of objects containing setting and value fields.
	 * @var Collection|GraphObject
	 */
	public $settings;

	/**
	 * The photos, videos, and posts in which the Page has been tagged.
	 *
	 * Returns a heterogeneous array of Photo, Video or Post objects.
	 * @var Collection|GraphObject
	 */
	public $tagged;

	/**
	 * The Page's posted links.
	 * @var Collection|Link
	 */
	public $links;

	/**
	 * The Page's uploaded photos.
	 *
	 * Returns array of Photo objects.
	 * @var Collection|GraphObject
	 */
	public $photos;

	/**
	 * Groups to which the Page belongs.
	 *
	 * Returns array containing group id, version, name and unread fields.
	 * @var Collection|GraphObject
	 */
	public $groups;

	/**
	 * The photo albums the Page has uploaded.
	 *
	 * Returns array of Album objects.
	 * @var Collection|GraphObject
	 */
	public $albums;

	/**
	 * The Page's status updates.
	 *
	 * Returns array of Status message objects.
	 * @var Collection|GraphObject
	 */
	public $statuses;

	/**
	 * The videos the Page has uploaded.
	 *
	 * Returns array of Video objects.
	 * @var Collection|GraphObject
	 */
	public $videos;

	/**
	 * The Page's notes.
	 *
	 * Returns An array of Note objects.
	 * @var Collection|GraphObject
	 */
	public $notes;

	/**
	 * The Page's own posts.
	 *
	 * Returns array of Post objects.
	 * @var Collection|GraphObject
	 */
	public $posts;

	/**
	 * The Page's questions.
	 *
	 * Returns array of Question objects.
	 * @var Collection|GraphObject
	 */
	public $questions;

	/**
	 * The events the Page is attending.
	 *
	 * Returns array containing event id, name, start_time, end_time, location and rsvp_status
	 * @var Collection|GraphObject
	 */
	public $events;

	/**
	 * Checkins made to this Place Page by the current user, and friends of the current user.
	 * Requires "user_checkins" or "friends_checkins" permissions.
	 *
	 * Returns array of Checkin objects.
	 * @var Collection|GraphObject
	 */
//	public $checkins;

	/**
	 * A list of the Page's Admins.
	 *
	 * Returns array of objects containing id, name.
	 * @var Collection|GraphObject
	 */
	public $admins;

	/**
	 * A list of the Page's conversations.
	 * Requires "read_mailbox" permissions.
	 * @var Collection|Message
	 */
	public $conversations;

	/**
	 * A list of the Page's milestones.
	 *
	 * Returns array of Milestone Objects.
	 * @var Collection|GraphObject
	 */
	public $milestones;

	/**
	 * A list of users blocked from the Page.
	 *
	 * Returns array of objects containing id, name.
	 * @var Collection|GraphObject
	 */
	public $blocked;

	/**
	 * The Page's profile tabs.
	 *
	 * Returns array of objects containing id, name, link, application, custom_name, is_permanent, position, and is_non_connection_landing_tab.
	 * @var Collection|GraphObject
	 */
	public $tabs;

	/**
	 * The Page's Insights data.
	 * Requires "read_insights" permissions.
	 *
	 * Returns array of Insights objects. See the Insights documentation for more information.
	 * @var Collection|GraphObject
	 */
	public $insights;

	/**
	 * Constructor
	 * @param mixed $id
	 * @param array $parameters
	 * @param bool $preload  true: Fetch fields now. false: Fetch fields when needed.
	 */
	function __construct($id, $parameters = null, $preload = false) {
		if ($id === null || is_array($id)) {
			parent::__construct($id, $parameters, $preload);
			return;
		}
		if ($parameters === null) { // Fetch all allowed fields?
			$parameters = array(
				'fields' => implode(',', $this->getAllowedFields(array('id' => $id))),
			);
		}
		parent::__construct($id, $parameters, $preload);
	}

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'feed' => array(),
			'picture' => array(),
			'settings' => array(),
			'tagged' => array(),
			'links' => array('class' => '\Sledgehammer\Facebook\Link'),
			'photos' => array(),
			'groups' => array(),
			'albums' => array(),
			'statuses' => array(),
			'videos' => array(),
			'notes' => array(),
			'posts' => array(),
			'questions' => array(),
			'events' => array(),
			'checkins' => array(),
			'admins' => array(),
			'conversations' => array('class' => '\Sledgehammer\Facebook\Message'),
			'milestones' => array(),
			'blocked' => array(),
			'tabs' => array(),
			'insights' => array(),
		);
		return $connections;
	}

}

?>