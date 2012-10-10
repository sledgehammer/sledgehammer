<?php
/**
 * Thread
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * A message thread in the new Facebook messaging system as represented in the Graph API. The User object has a threads connections.
 * Requires "read_mailbox" permissions.
 *
 * @link https://developers.facebook.com/docs/reference/api/thread/
 * @package Facebook
 */
class Thread extends \Sledgehammer\GraphObject {

	/**
	 * The unique ID for this message thread.
	 * @var string
	 */
	public $id;

	/**
	 * Fragment of the thread for use in thread lists.
	 * @var string
	 */
	public $snippet;

	/**
	 * Timestamp of when the thread was last updated.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $updated_time;

	/**
	 * Number of messages in the thread.
	 *
	 * integer
	 */
	public $message_count;

	/**
	 * Number of unread messages in the thread.
	 *
	 * integer
	 */
	public $unread_count;

	/**
	 * Thread tags.
	 *
	 * array of objects containing name
	 */
	public $tags;

	/**
	 * List of the thread participants.
	 *
	 * array of objects each containing name, email, and Facebook id (if available)
	 */
	public $participants;

	/**
	 * List of former thread participants who have unsubscribed from the thread.
	 *
	 * array of objects each containing name, email, and Facebook id (if available)
	 */
	public $former_participants;

	/**
	 * List of participants who have sent a message in the thread.
	 *
	 * array of objects each containing name, email, and Facebook id (if available)
	 */
	public $senders;

	/**
	 * List of the message objects contained in this thread.
	 *
	 * array of message objects
	 */
	public $messages;

	/**
	 * Thread tags.
	 *
	 * Returns array of objects containing name
	 * @var Collection|GraphObject
	 */
//	public $tags;

	/**
	 * List of the thread participants.
	 *
	 * Returns array of objects each containing name, email, and Facebook id (if available).
	 * @var Collection|GraphObject
	 */
//	public $participants;

	/**
	 * List of former thread participants who have unsubscribed from the thread.
	 *
	 * Returns array of objects each containing name, email, and Facebook id (if available).
	 * @var Collection|GraphObject
	 */
//	public $former_participants;

	/**
	 * List of participants who have sent a message in the thread.
	 *
	 * Returns array of objects each containing name, email, and Facebook id (if available).
	 * @var Collection|GraphObject
	 */
//	public $senders;

	/**
	 * List of the message objects contained in this thread.
	 * @var Collection|Message
	 */
//	public $messages;

	protected static function getKnownConnections($options = array()) {
		$connections = array(
			'tags' => array(),
			'participants' => array(),
			'former_participants' => array(),
			'senders' => array(),
			'messages' => array('class' => '\Sledgehammer\Facebook\Message'),
		);
		return $connections;
	}

}

?>