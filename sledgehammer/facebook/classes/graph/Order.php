<?php
/**
 * Order
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * You can use the order object to interact with orders created by the application using Facebook credits to view and update orders as needed.
 *
 * @link https://developers.facebook.com/docs/reference/api/order/
 * @package Facebook
 */
class Order extends \Sledgehammer\GraphObject {

	/**
	 * id for the order.
	 *
	 * name and id of the user
	 */
	public $id;

	/**
	 * user associated with the order.
	 * @var string
	 */
	public $from;

	/**
	 * amount for the order.
	 *
	 * integer
	 */
	public $amount;

	/**
	 * status the order.
	 *
	 * string - possible values are placed, settled, disputed,  refunded, cancelled
	 */
	public $status;

	/**
	 * application associated with the order.
	 *
	 * name and id of the application
	 */
	public $application;

	/**
	 * country associated with the order.
	 *
	 * String containing ISO 3166 alpha 2 code representing the country
	 */
	public $country;

	/**
	 * refund reason code if the order was refunded by Facebook.
	 * @var string
	 */
	public $refund_reason_code;

	/**
	 * time when the order was created.
	 *
	 * string containing an ISO-8601 datetime
	 */
	public $created_time;

	/**
	 * time when the order was last updated.
	 *
	 * string containing an ISO-8601 datetime
	 */
	public $updated_time;

}

?>