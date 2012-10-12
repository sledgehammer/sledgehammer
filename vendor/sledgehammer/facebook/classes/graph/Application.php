<?php
/**
 * Application
 */
namespace Sledgehammer\Facebook;

use Sledgehammer\Collection;
use Sledgehammer\GraphObject;

/**
 * An application registered on Facebook Platform as represented in the Graph API. Applications a user administers can be retrieved via the /accounts connection on the User object.
 *
 * @link https://developers.facebook.com/docs/reference/api/application/
 * @package Facebook
 */
class Application extends \Sledgehammer\GraphObject {

	/**
	 * The application ID.
	 * @var string
	 */
	public $id;

	/**
	 * The title of the application.
	 * @var string
	 */
	public $name;

	/**
	 * The description of the application written by the 3rd party developers.
	 * @var string
	 */
	public $description;

	/**
	 * The category of the application.
	 * @var string
	 */
	public $category;

	/**
	 * The company the application belongs to.
	 * @var string
	 */
	public $company;

	/**
	 * The URL of the application's icon.
	 * @var string
	 */
	public $icon_url;

	/**
	 * The subcategory of the application.
	 * @var string
	 */
	public $subcategory;

	/**
	 * A link to the Application's profile page.
	 * @var string
	 */
	public $link;

	/**
	 * The URL of the application's logo.
	 * @var string
	 */
	public $logo_url;

	/**
	 * The number of daily active users the application has.
	 * @var string
	 */
	public $daily_active_users;

	/**
	 * The number of weekly active users the application has.
	 * @var string
	 */
	public $weekly_active_users;

	/**
	 * The number of monthly active users the application has.
	 * @var string
	 */
	public $monthly_active_users;

	/**
	 * Migrations settings for app profile (Editable via API).
	 *
	 * array
	 */
	public $migrations;

	/**
	 * The namespace for the app (Editable via API).
	 * @var string
	 */
	public $namespace;

	/**
	 * Demographic restrictions set for this app (Editable via API).
	 *
	 * Object with one or more of the following fields: type, location, age, and age_distr
	 */
	public $restrictions;

	/**
	 * Domains and subdomains this app can use (Editable via API).
	 *
	 * array
	 */
	public $app_domains;

	/**
	 * The URL of a special landing page that helps users of an app begin publishing Open Graph activity (Editable via API).
	 * @var string
	 */
	public $auth_dialog_data_help_url;

	/**
	 * The description of an app that appears in the Auth Dialog (Editable via API).
	 * @var string
	 */
	public $auth_dialog_description;

	/**
	 * One line description of an app that appears in the Auth Dialog (Editable via API).
	 * @var string
	 */
	public $auth_dialog_headline;

	/**
	 * The text to explain why an app needs additional permissions that appears in the Auth Dialog (Editable via API).
	 * @var string
	 */
	public $auth_dialog_perms_explanation;

	/**
	 * Basic user permissions that a user must grant when Authenticated Referrals are enabled (Editable via API).
	 *
	 * array
	 */
	public $auth_referral_user_perms;

	/**
	 * Basic friends permissions that a user must grant when Authenticated Referrals are enabled (Editable via API).
	 *
	 * array
	 */
	public $auth_referral_friend_perms;

	/**
	 * The default privacy setting selected for Open Graph activities in the Auth Dialog (Editable via API).
	 *
	 * string which is one of: SELF, EVERYONE, ALL_FRIENDS or NONE
	 */
	public $auth_referral_default_activity_privacy;

	/**
	 * Indicates whether Authenticated Referrals are enabled (Editable via API).
	 *
	 * bool
	 */
	public $auth_referral_enabled;

	/**
	 * Extended permissions that a user can choose to grant when Authenticated Referrals are enabled (Editable via API).
	 *
	 * array
	 */
	public $auth_referral_extended_perms;

	/**
	 * The format that an app receives the Auth token from the Auth Dialog in (Editable via API).
	 *
	 * string which is one of: code or token
	 */
	public $auth_referral_response_type;

	/**
	 * Indicates whether app uses fluid or settable height values for Canvas (Editable via API).
	 *
	 * bool
	 */
	public $canvas_fluid_height;

	/**
	 * Indicates whether app uses fluid or fixed width values for Canvas (Editable via API).
	 *
	 * bool
	 */
	public $canvas_fluid_width;

	/**
	 * The non-secure URL from which Canvas app content is loaded (Editable via API).
	 * @var string
	 */
	public $canvas_url;

	/**
	 * Email address listed for users to contact developers (Editable via API).
	 * @var string
	 */
	public $contact_email;

	/**
	 * Unix timestamp that indicates when the app was created.
	 *
	 * int
	 */
	public $created_time;

	/**
	 * User ID of the creator of this app.
	 *
	 * int
	 */
	public $creator_uid;

	/**
	 * URL that is pinged whenever a user removes the app (Editable via API).
	 * @var string
	 */
	public $deauth_callback_url;

	/**
	 * ID of the app in the iPhone App Store.
	 * @var string
	 */
	public $iphone_app_store_id;

	/**
	 * Webspace created with one of our hosting partners for this app.
	 * @var string
	 */
	public $hosting_url;

	/**
	 * URL to which Mobile users will be directed when using the app (Editable via API).
	 * @var string
	 */
	public $mobile_web_url;

	/**
	 * The title of the app when used in a Page Tab (Editable via API).
	 * @var string
	 */
	public $page_tab_default_name;

	/**
	 * The non-secure URL from which Page Tab app content is loaded (Editable via API).
	 * @var string
	 */
	public $page_tab_url;

	/**
	 * The URL that links to a Privacy Policy for the app (Editable via API).
	 * @var string
	 */
	public $privacy_policy_url;

	/**
	 * The secure URL from which Canvas app content is loaded (Editable via API).
	 * @var string
	 */
	public $secure_canvas_url;

	/**
	 * The secure URL from which Page Tab app content is loaded (Editable via API).
	 * @var string
	 */
	public $secure_page_tab_url;

	/**
	 * App requests must originate from this comma-separated list of IP addresses (Editable via API).
	 * @var string
	 */
	public $server_ip_whitelist;

	/**
	 * Indicates whether app usage stories show up in the Ticker or News Feed (Editable via API).
	 *
	 * bool
	 */
	public $social_discovery;

	/**
	 * URL to Terms of Service which is linked to in Auth Dialog (Editable via API).
	 * @var string
	 */
	public $terms_of_service_url;

	/**
	 * Main contact email for this app (Editable via API).
	 * @var string
	 */
	public $user_support_email;

	/**
	 * URL of support for users of an app shown in Canvas footer (Editable via API).
	 * @var string
	 */
	public $user_support_url;

	/**
	 * URL of a website that integrates with this app (Editable via API).
	 * @var string
	 */
	public $website_url;

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

}

?>