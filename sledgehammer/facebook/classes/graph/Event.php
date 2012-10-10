<?php
/**
 * Event
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * Specifies information about an event, including the location, event name, and which invitees plan to attend. The User, Page, and Application objects have an events connection.
 * Requires "user_events" or "friends_events" permissions.
 *
 * @link https://developers.facebook.com/docs/reference/api/event/
 * @package Facebook
 */
class Event extends \Sledgehammer\GraphObject {

	/**
	 * The event ID.
	 * @var string
	 */
	public $id;

	/**
	 * The profile that created the event.
	 *
	 * object containing id and name fields
	 */
	public $owner;

	/**
	 * The event title.
	 * @var string
	 */
	public $name;

	/**
	 * The long-form description of the event.
	 * @var string
	 */
	public $description;

	/**
	 * The start time of the event, as you want it to be displayed on facebook.
	 *
	 * string containing an ISO-8601 formatted date/time or a UNIX timestamp; if it contains a time zone (not recommended), it will be converted to Pacific time before being stored and displayed
	 */
	public $start_time;

	/**
	 * The end time of the event, as you want it to be displayed on facebook.
	 *
	 * string containing an ISO-8601 formatted date/time or a UNIX timestamp; if it contains a time zone (not recommended), it will be converted to Pacific time before being stored and displayed
	 */
	public $end_time;

	/**
	 * The location for this event.
	 * @var string
	 */
	public $location;

	/**
	 * The location of this event.
	 *
	 * object containing one or more of the following fields: id, street, city, state, zip, country, latitude, and longitude fields
	 */
	public $venue;

	/**
	 * The visibility of this event.
	 *
	 * string containing 'OPEN', 'CLOSED', or 'SECRET'
	 */
	public $privacy;

	/**
	 * The last time the event was updated.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $updated_time;

	/**
	 * The URL of the event's picture (only returned if you explicitly include picture in the fields param; example: ?fields=id,name,picture).
	 * @var string
	 */
	public $picture;

	/**
	 * This event's wall.
	 *
	 * Returns An array of Post objects.
	 * @var Collection|GraphObject
	 */
	public $feed;

	/**
	 * All of the users who have been not yet responded to their invitation to this event.
	 *
	 * Returns array containing objects with id, name and rsvp_status fields.
	 * @var Collection|GraphObject
	 */
	public $noreply;

	/**
	 * All of the users who have been invited to this event.
	 *
	 * Returns array containing objects with id, name and rsvp_status fields.
	 * @var Collection|GraphObject
	 */
	public $invited;

	/**
	 * All of the users who are attending this event.
	 *
	 * Returns array containing objects with id, name and rsvp_status fields.
	 * @var Collection|GraphObject
	 */
	public $attending;

	/**
	 * All of the users who have been responded "Maybe" to their invitation to this event.
	 *
	 * Returns array containing objects with id, name and rsvp_status fields.
	 * @var Collection|GraphObject
	 */
	public $maybe;

	/**
	 * All of the users who declined their invitation to this event.
	 *
	 * Returns array containing JSON objects with id, name and rsvp_status fields.
	 * @var Collection|GraphObject
	 */
	public $declined;

	/**
	 * The event's profile picture.
	 *
	 * Returns Returns a HTTP 302 with the URL of the event's picture (use ?type=small | normal | large to request a different photo).
	 * @var Collection|GraphObject
	 */
//	public $picture;

	/**
	 * The videos uploaded to an event.
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
			);
		}
		parent::__construct($id, $parameters, $preload);
	}

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'feed' => array(),
			'noreply' => array(),
			'invited' => array(),
			'attending' => array(),
			'maybe' => array(),
			'declined' => array(),
			'picture' => array(),
			'videos' => array(),
		);
		return $connections;
	}

}

?>