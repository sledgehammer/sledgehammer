Minify
-------
A Sledgehammer module that minifies the css and js files.
If will scan the "public" folders inside the module and application folders for *.js and *.css files and outputs minified version into into the /public folder.

This means you'll only need the human-readable version in source-control.
You can develop/debug using the human-readable or minified version.

Uses [jsminplus](http://crisp.tweakblogs.net/blog/cat/716) for javascript and [cssmin](http://code.google.com/p/cssmin/) for css