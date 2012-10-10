# Facebook

Object oriented API for using Facebook's Social Graph.

## Features
* Autocompletion for common facebook objects.
* Automaticly fetches all allowed fields.
* Gives hint which permissions are missing when you access a (maybe) not-allowed property or connection.
* Fetches connected objects as properties. $me->friends
* Connected objects are Collection object, for easy filtering & sorting. $me->friends->orderBy('last_name');
* Caches userfields and friends when using the FacebookUser object for improved performance.
* Lazily fetches objectdata. No api calls for data you don't use.

## Improvements in the Facebook class.

 * Easy login() or automatic login.
 * shorthand for get/post/delete requests. Example: Facebook::get('me')
 * shorthand for executing a fql query.
 * Validates requested permissions.
 * Logs api calls and measures executionTime.
 * api() accepts $parameters['fields'] as an array.
 * all() fetches multiple pages in a paginated result and returns the merged array.
 * Singleton pattern, access Facebook from anywhere in your application.
 * Add "local_cache"=> true to the parameters and the the results of the api call are cached.

## Idea's / Todo

* notice() when the (automatic) pageLimit is reached.
* Implement all documented facebook entities & connections.
* Implement writing api as methods in the GraphObject.
* Implement ActiveRecord methods. save(), delete()
* Greedy mode (vs Strict mode) which retrieves all fields we might*1 have access to. (*1: depends on privacy settings)
* Ability to make asynchronous requests. "async" => true