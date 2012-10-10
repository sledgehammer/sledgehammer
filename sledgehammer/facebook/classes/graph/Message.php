<?php
/**
 * Message
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * An individual message in the new Facebook messaging system.  Every message has a message ID that represents an object in the Graph.  To get access to read a user's messages, you should request the read_mailbox Extended Permission.
 * Requires "read_mailbox" permissions.
 *
 * @link https://developers.facebook.com/docs/reference/api/message/
 * @package Facebook
 */
class Message extends \Sledgehammer\GraphObject {

	/**
	 * The unique ID for this message.
	 * @var string
	 */
	public $id;

	/**
	 * A timestamp of when this message was created.
	 *
	 * string containing ISO-8601 date-time
	 */
	public $created_time;

	/**
	 * The sender of this message.
	 *
	 * Object containing name and Facebook id (if available)
	 */
	public $from;

	/**
	 * A list of the message recipients.
	 *
	 * Array of objects each containing name and Facebook id (if available)
	 */
	public $to;

	/**
	 * The text of the message.
	 * @var string
	 */
	public $message;

}

?>