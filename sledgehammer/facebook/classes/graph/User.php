<?php
/**
 * User
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Facebook;
use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * A user profile as represented in the Graph API.
 *
 * @link https://developers.facebook.com/docs/reference/api/user/
 * @package Facebook
 */
class User extends \Sledgehammer\GraphObject {

	/**
	 * The user's Facebook ID.
	 * @var string
	 */
	public $id;

	/**
	 * The user's full name.
	 * @var string
	 */
	public $name;

	/**
	 * The user's first name.
	 * @var string
	 */
	public $first_name;

	/**
	 * The user's middle name.
	 * @var string
	 */
	public $middle_name;

	/**
	 * The user's last name.
	 * @var string
	 */
	public $last_name;

	/**
	 * The user's gender: female or male.
	 * @var string
	 */
	public $gender;

	/**
	 * The user's locale.
	 *
	 * string containing the ISO Language Code and ISO Country Code
	 */
	public $locale;

	/**
	 * The user's languages.
	 * Requires "user_likes" permissions.
	 *
	 * array of objects containing language id and name
	 */
	public $languages;

	/**
	 * The URL of the profile for the user on Facebook.
	 *
	 * string containing a valid URL
	 */
	public $link;

	/**
	 * The user's Facebook username.
	 * @var string
	 */
	public $username;

	/**
	 * An anonymous, but unique identifier for the user; only returned if specifically requested via the fields URL parameter.
	 * @var string
	 */
	public $third_party_id;

	/**
	 * Specifies whether the user has installed the application associated with the app access token that is used to make the request; only returned if specifically requested via the fields URL parameter.
	 *
	 * object containing type (this is always "user"), id (the ID of the user), and optional installed field (always true if returned); The installed field is only returned if the user has installed the application, otherwise it is not part of the returned object
	 */
	public $installed;

	/**
	 * The user's timezone offset from UTC.
	 * @var number
	 */
	public $timezone;

	/**
	 * The last time the user's profile was updated; changes to the languages, link, timezone, verified, interested_in, favorite_athletes, favorite_teams, and video_upload_limits are not not reflected in this value.
	 *
	 * string containing an ISO-8601 datetime
	 */
	public $updated_time;

	/**
	 * The user's account verification status, either true or false (see below).
	 * @var boolean
	 */
	public $verified;

	/**
	 * The user's biography.
	 * Requires "user_about_me" or "friends_about_me" permissions.
	 * @var string
	 */
	public $bio;

	/**
	 * The user's birthday.
	 * Requires "user_birthday" or "friends_birthday" permissions.
	 *
	 * Date string in MM/DD/YYYY format
	 */
	public $birthday;

	/**
	 * The user's cover photo (must be explicitly requested using fields=cover parameter).
	 *
	 * array of fields id, source, and offset_y
	 */
	public $cover;

	/**
	 * The user's currency settings (must be explicitly requested using a fields=currency URL parameter).
	 *
	 * object with fields currency (detailed here), id
	 */
//	public $currency;

	/**
	 * A list of the user's devices beyond desktop.
	 *
	 * array of objects containing os which may be a value of 'iOS' or 'Android', along with an additional field hardware which may be a value of 'iPad' or 'iPhone' if present, however may not be returned if we are unable to determine the hardware model - Note: this is a non-default field and must be explicitly specified as shown below
	 */
	public $devices;

	/**
	 * A list of the user's education history.
	 * Requires "user_education_history" or "friends_education_history" permissions.
	 *
	 * array of objects containing year and type fields, and school object (name,  id, type, and optional year, degree, concentration array, classes array, and with array )
	 */
	public $education;

	/**
	 * The proxied or contact email address granted by the user.
	 * Requires "email" permissions.
	 *
	 * string containing a valid RFC822 email address
	 */
	public $email;

	/**
	 * The user's hometown.
	 * Requires "user_hometown" or "friends_hometown" permissions.
	 *
	 * object containing name and id
	 */
	public $hometown;

	/**
	 * The genders the user is interested in.
	 * Requires "user_relationship_details" or "friends_relationship_details" permissions.
	 *
	 * array containing strings
	 */
	public $interested_in;

	/**
	 * The user's current city.
	 * Requires "user_location" or "friends_location" permissions.
	 *
	 * object containing name and id
	 */
	public $location;

	/**
	 * The user's political view.
	 * Requires "user_religion_politics" or "friends_religion_politics" permissions.
	 * @var string
	 */
	public $political;

	/**
	 * The user's favorite athletes; this field is deprecated and will be removed in the near future.
	 * Requires "user_likes" or "friends_likes" permissions.
	 *
	 * array of objects containing id and name fields
	 */
	public $favorite_athletes;

	/**
	 * The user's favorite teams; this field is deprecated and will be removed in the near future.
	 * Requires "user_likes" or "friends_likes" permissions.
	 *
	 * array of objects containing id and name fields
	 */
	public $favorite_teams;

	/**
	 * The URL of the user's profile pic (only returned if you explicitly specify a 'fields=picture' param).
	 * @var string
	 */
	public $picture;

	/**
	 * The user's favorite quotes.
	 * Requires "user_about_me" or "friends_about_me" permissions.
	 * @var string
	 */
	public $quotes;

	/**
	 * The user's relationship status: Single, In a relationship, Engaged, Married, It's complicated, In an open relationship, Widowed, Separated, Divorced, In a civil union, In a domestic partnership.
	 * Requires "user_relationships" or "friends_relationships" permissions.
	 * @var string
	 */
	public $relationship_status;

	/**
	 * The user's religion.
	 * Requires "user_religion_politics" or "friends_religion_politics" permissions.
	 * @var string
	 */
	public $religion;

	/**
	 * The user's significant other.
	 * Requires "user_relationships" or "friends_relationships" permissions.
	 *
	 * object containing name and id
	 */
	public $significant_other;

	/**
	 * The size of the video file and the length of the video that a user can upload; only returned if specifically requested via the fields URL parameter.
	 *
	 * object containing length and size of video
	 */
	public $video_upload_limits;

	/**
	 * The URL of the user's personal website.
	 * Requires "user_website" or "friends_website" permissions.
	 *
	 * string containing a valid URL
	 */
	public $website;

	/**
	 * A list of the user's work history.
	 * Requires "user_work_history" or "friends_work_history" permissions.
	 *
	 * array of objects containing employer, location, position, start_date and end_date fields
	 */
	public $work;

	/**
	 * The Facebook apps and pages owned by the current user.
	 * Requires "manage_pages" permissions.
	 *
	 * Returns array of objects containing account name, access_token, category, id
	 * @var Collection|GraphObject
	 */
	public $accounts;

	/**
	 * The achievements for the user.
	 * Requires "user_games_activity" or "friends_games_activity" permissions.
	 * @var Collection|Achievement
	 */
	public $achievements;

	/**
	 * The activities listed on the user's profile.
	 * Requires "user_activities" or "friends_activities" permissions.
	 *
	 * Returns array of objects containing activity id, name, category and create_time fields.
	 * @var Collection|GraphObject
	 */
	public $activities;

	/**
	 * The photo albums this user has created.
	 * Requires "user_photos" or "friends_photos" permissions.
	 *
	 * Returns array of Album objects.
	 * @var Collection|GraphObject
	 */
	public $albums;

	/**
	 * The user's outstanding requests from an app.
	 *
	 * Returns array of app requests for the user within that app.
	 * @var Collection|GraphObject
	 */
	public $apprequests;

	/**
	 * The books listed on the user's profile.
	 * Requires "user_likes" or "friends_likes" permissions.
	 * @var Collection|Page
	 */
	public $books;

	/**
	 * The places that the user has checked-into.
	 * Requires "user_checkins" or "friends_checkins" permissions.
	 *
	 * Returns array of Checkin objects
	 * @var Collection|GraphObject
	 */
	public $checkins;

	/**
	 * The events this user is attending.
	 * Requires "user_events" or "friends_events" permissions.
	 * @var Collection|Event
	 */
	public $events;

	/**
	 * The user's family relationships.
	 * Requires "user_relationships" permissions.
	 *
	 * Returns array of objects containing id, name, and relationship fields.
	 * @var Collection|GraphObject
	 */
	public $family;

	/**
	 * The user's wall.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of Post objects containing (up to) the last 25 posts.
	 * @var Collection|GraphObject
	 */
	public $feed;

	/**
	 * The user's friend lists.
	 * Requires "read_friendlists" permissions.
	 *
	 * Returns array of objects containing id and name fields of the friendlist.
	 * @var Collection|GraphObject
	 */
	public $friendlists;

	/**
	 * The user's incoming friend requests.
	 * Requires "user_requests" permissions.
	 *
	 * Returns array of objects containing to, from, message, created_time and unread fields of the friend request
	 * @var Collection|GraphObject
	 */
	public $friendrequests;

	/**
	 * The user's friends.
	 * @var Collection|User
	 */
	public $friends;

	/**
	 * Games the user has added to the Arts and Entertainment section of their profile.
	 * Requires "user_likes" permissions.
	 *
	 * Returns array of objects containing id, name, category, and created_time
	 * @var Collection|GraphObject
	 */
	public $games;

	/**
	 * The Groups that the user belongs to.
	 * Requires "user_groups" or "friends_groups" permissions.
	 *
	 * Returns An array of objects containing the version(old-0 or new Group-1), name, id, administrator (if user is the administrator of the Group) and bookmark_order(at what place in the list of group bookmarks on the homepage, the group shows up for the user).
	 * @var Collection|GraphObject
	 */
	public $groups;

	/**
	 * The user's news feed.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of Post objects containing (up to) the last 25 posts.
	 * @var Collection|GraphObject
	 */
	public $home;

	/**
	 * The Threads in this user's inbox.
	 * Requires "read_mailbox" permissions.
	 * @var Collection|Thread
	 */
	public $inbox;

	/**
	 * The interests listed on the user's profile.
	 * Requires "user_interests" or "friends_interests" permissions.
	 *
	 * Returns array of objects containing interest id, name, category and create_time fields.
	 * @var Collection|GraphObject
	 */
	public $interests;

	/**
	 * All the pages this user has liked.
	 * Requires "user_likes" or "friends_likes" permissions.
	 * @var Collection|Page
	 */
	public $likes;

	/**
	 * The user's posted links.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of Link objects.
	 * @var Collection|GraphObject
	 */
	public $links;

	/**
	 * Posts, statuses, and photos in which the user has been tagged at a location, or where the user has authored content (i.e. this excludes objects with no location information, and objects in which the user is not tagged). See documentation of the location_post table for more detailed information on permissions.
	 * Requires "user_photos", "friend_photos", "user_status", "friends_status", "user_checkins" or "friends_checkins" permissions.
	 *
	 * Returns array of objects containing the id, type, place, created_time, and optional application and tags fields.
	 * @var Collection|GraphObject
	 */
	public $locations;

	/**
	 * The movies listed on the user's profile.
	 * Requires "user_likes" or "friends_likes" permissions.
	 * @var Collection|Page
	 */
	public $movies;

	/**
	 * The music listed on the user's profile.
	 * Requires "user_likes" or "friends_likes" permissions.
	 * @var Collection|Page
	 */
	public $music;

	/**
	 * The mutual friends between two users.
	 * @var Collection|User
	 */
	public $mutualfriends;

	/**
	 * The user's notes.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of Note objects.
	 * @var Collection|GraphObject
	 */
	public $notes;

	/**
	 * The notifications for the user.
	 * Requires "manage_notifications" permissions.
	 *
	 * Returns array of objects containing id, from, to, created_time,updated_time, title, link, application, unread.
	 * @var Collection|GraphObject
	 */
	public $notifications;

	/**
	 * The messages in this user's outbox.
	 * Requires "read_mailbox" permissions.
	 *
	 * Returns array of messages
	 * @var Collection|GraphObject
	 */
	public $outbox;

	/**
	 * The Facebook Credits orders the user placed with an application. See the Credits API for more information.
	 * @var Collection|Order
	 */
	public $payments;

	/**
	 * The permissions that user has granted the application.
	 *
	 * Returns array containing a single object which has the keys as the permission names and the values as the permission values (1/0) - Permissions with value 0 are omitted from the object by default; also includes a type field  which is always permissions if the query param metadata=1 is passed.
	 * @var Collection|GraphObject
	 */
	public $permissions;

	/**
	 * Photos the user (or friend) is tagged in.
	 * Requires "user_photo_video_tags" or "friends_photo_video_tags" permissions.
	 *
	 * Returns array of Photo objects.
	 * @var Collection|GraphObject
	 */
	public $photos;

	/**
	 * The user's profile picture.
	 *
	 * Returns HTTP 302 redirect to URL of the user's profile picture (use ?type=square | small | normal | large to request a different photo).
	 * @var Collection|GraphObject
	 */
//	public $picture;

	/**
	 * The user's pokes.
	 * Requires "read_mailbox" permissions.
	 *
	 * Returns an array of objects containing to, from, created_time and type fields.
	 * @var Collection|GraphObject
	 */
	public $pokes;

	/**
	 * The user's own posts.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of Post objects.
	 * @var Collection|GraphObject
	 */
	public $posts;

	/**
	 * The user's questions.
	 * Requires "user_questions" permissions.
	 *
	 * Returns array of Question objects.
	 * @var Collection|GraphObject
	 */
	public $questions;

	/**
	 * The current scores for the user in games.
	 * Requires "user_games_activity" or "friends_games_activity" permissions.
	 *
	 * Returns array of objects containing user, application, score and type.
	 * @var Collection|GraphObject
	 */
	public $scores;

	/**
	 * The user's status updates.
	 * Requires "read_stream" permissions.
	 *
	 * Returns An array of Status message objects.
	 * @var Collection|GraphObject
	 */
	public $statuses;

	/**
	 * People you're subscribed to.
	 * @var Collection|User
	 */
	public $subscribedto;

	/**
	 * The user's subscribers.
	 * @var Collection|User
	 */
	public $subscribers;

	/**
	 * Posts the user is tagged in.
	 * Requires "read_stream" permissions.
	 *
	 * Returns array of objects containing id, from, to, picture, link, name, caption, description, properties, icon, actions, type, application, created_time, and updated_time
	 * @var Collection|GraphObject
	 */
	public $tagged;

	/**
	 * The television listed on the user's profile.
	 * Requires "user_likes" or "friends_likes" permissions.
	 * @var Collection|Page
	 */
	public $television;

	/**
	 * The updates in this user's inbox.
	 * Requires "read_mailbox" permissions.
	 *
	 * Returns array of messages
	 * @var Collection|GraphObject
	 */
	public $updates;

	/**
	 * The videos this user has been tagged in.
	 * Requires "user_videos" or "friends_videos" permissions.
	 *
	 * Returns array of Video objects.
	 * @var Collection|GraphObject
	 */
	public $videos;

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
				'local_cache' => true
			);
		}
		parent::__construct($id, $parameters, $preload);
	}

	/**
	 * Post a link or status-message to the users feed.
	 * @permission publish_stream
	 *
	 * @param Facebook\Post|GraphObject|array $post
	 * @return Facebook\Post
	 */
	function postToFeed($post) {
		if (is_array($post)) {
			$post = new Facebook\Post($post);
		}
		$fb = Facebook::getInstance();
		if (in_array('publish_stream', $fb->getPermissions()) === false) {
			notice('Posting to the user\'s feed requires the "publish_stream" permission', 'Current permissions: '.quoted_human_implode(' and ', $fb->getPermissions()));
		}
		$response = $fb->post($this->id.'/feed', $post);
		$class = get_class($post);
		return new $class($response['id']);
	}

	/**
	 * List of two Users mutual friends.
	 * @param number $userId
	 * @return Collection|Facebook\User
	 */
	function getMutualfriendWith($userId) {
		$response = Facebook::all($this->id.'/mutualfriends/'.$userId, array('fields' => $this->getAllowedFields()));
		$friends = array();
		foreach ($response as $friend) {
			$friends[] = new Facebook\User($friend);
		}
		return new Collection($friends);
	}

	function __get($property) {
		if ($property === 'friends') {
			$parameters = array(
				'fields' => $this->getAllowedFields(array('friend' => true)),
				'local_cache' => true
			);
			$this->__set(array($property => $this->getFriends($parameters)));
			return $this->$property;
		}
		if ($property === 'mutualfriends') {
			$this->__set(array($property => $this->getMutualfriendWith(Facebook::me()->id)));
			return $this->$property;
		}
		return parent::__get($property);
	}

	protected static function getFieldPermissions($options = array()) {
		$permissions = array(
			'bio' => 'about_me',
			'birthday' => 'birthday',
			'education' => 'education_history',
			'hometown' => 'hometown',
			'interested_in' => 'relationship_details',
			'location' => 'location',
			'political' => 'religion_politics',
			'quotes' => 'about_me',
			'relationship_status' => 'relationships',
			'religion' => 'religion_politics',
			'significant_other' => 'relationships',
			'website' => 'website',
			'work' => 'work_history',
		);
		if (isset($options['id']) && ($options['id'] === 'me' || $options['id'] === Facebook::getInstance()->getUser())) { // Current user?
			foreach ($permissions as $property => $permission) {
				$permissions[$property] = 'user_'.$permission;
			}
			$permissions['languages'] = 'user_likes';
			$permissions['email'] = 'email';
		} else {
			// @todo detect if it's a friend
			foreach ($permissions as $property => $permission) {
				$permissions[$property] = 'friends_'.$permission;
			}
			$permissions['email'] = 'denied';
			$permissions['languages'] = 'denied';
		}
		return $permissions;
	}

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'accounts' => array(),
			'achievements' => array('class' => '\Sledgehammer\Facebook\Achievement'),
			'activities' => array(),
			'albums' => array(),
			'apprequests' => array(),
			'books' => array('class' => '\Sledgehammer\Facebook\Page'),
			'checkins' => array(),
			'events' => array('class' => '\Sledgehammer\Facebook\Event'),
			'family' => array(),
			'feed' => array(),
			'friendlists' => array(),
			'friendrequests' => array(),
			'friends' => array('class' => '\Sledgehammer\Facebook\User'),
			'games' => array(),
			'groups' => array(),
			'home' => array(),
			'inbox' => array('class' => '\Sledgehammer\Facebook\Thread'),
			'interests' => array(),
			'likes' => array('class' => '\Sledgehammer\Facebook\Page'),
			'links' => array(),
			'locations' => array(),
			'movies' => array('class' => '\Sledgehammer\Facebook\Page'),
			'music' => array('class' => '\Sledgehammer\Facebook\Page'),
			'mutualfriends' => array('class' => '\Sledgehammer\Facebook\User'),
			'notes' => array(),
			'notifications' => array(),
			'outbox' => array(),
			'payments' => array('class' => '\Sledgehammer\Facebook\Order'),
			'permissions' => array(),
			'photos' => array(),
			'picture' => array(),
			'pokes' => array(),
			'posts' => array(),
			'questions' => array(),
			'scores' => array(),
			'statuses' => array(),
			'subscribedto' => array('class' => '\Sledgehammer\Facebook\User'),
			'subscribers' => array('class' => '\Sledgehammer\Facebook\User'),
			'tagged' => array(),
			'television' => array('class' => '\Sledgehammer\Facebook\Page'),
			'updates' => array(),
			'videos' => array(),
		);
		// user_* / friend_* permissions
		$permissions = array(
			'achievements' => 'games_activity',
			'activities' => 'activities',
			'albums' => 'photos',
			'books' => 'likes',
			'checkins' => 'checkins',
			'events' => 'events',
			'groups' => 'groups',
			'interests' => 'interests',
			'likes' => 'likes',
			//locations => photos, status and/or checkins.
			'movies' => 'likes',
			'music' => 'likes',
			'photos' => 'photo_video_tags',
			'scores' => 'games_activity',
			'television' => 'likes',
			'videos' => 'videos',
		);
		// only available for loggedin user
		$userPermissions = array(
			'home' => 'read_stream',
			'inbox' => 'read_mailbox',
			'family' => 'user_relationships',
			'feed' => 'read_stream',
			'friendlists' => 'read_friendlists',
			'friendrequests' => 'user_requests',
			'games' => 'likes',
			'links' => 'read_stream',
			'notes' => 'read_stream',
			'notifications' => 'manage_notifications',
			'outbox' => 'read_mailbox',
			'pokes' => 'read_mailbox',
			'questions' => 'user_questions',
			'statuses' => 'read_stream',
			'tagged' => 'read_stream',
			'updates' => 'read_mailbox',
		);
		if (isset($options['id']) && ($options['id'] === 'me' || $options['id'] === Facebook::getInstance()->getUser())) { // Current user?
			$prefix = 'user_';
			foreach ($userPermissions as $connection => $permission) {
				$connections[$connection]['permission'] = $permission;
			}
		} else {
			// @todo check if it's a friend
			$prefix = 'friends_';
		}
		foreach ($permissions as $connection => $permission) {
			$connections[$connection]['permission'] = $prefix.$permission;
		}
		return $connections;
	}

}

?>
