Build system for PHP 7 projects.
================================

This project provides a system for defining and running client-side build processes automatically, using tools already installed by your favourite client-side dependency manager.

***

// TODO: Status badges.

Example usage
-------------

build.json:

```json
{
	"src/script/**/*.js": {
		"name": "Babel transpile",
		"command": "./node_modules/.bin/babel",
		"args": "src/script/main.js -o www/script.js",
		"requires": {
			"node": "^8.4",
			"@command": "^6.0"
		}
	},
	
	"src/style/**/*.scss": {
		"name": "Sass compilation",
		"command": "sass",
		"args": "src/style/main.scss www/style.css",
		"requires": {
			"ruby": "2.4.2",
			"@command": "3.5.1"
		}
	},
	
	"src/page/**/*.{html|php}": {
		"name": "Sitemap generation",
		"command": "php vendor/bin/sitemap",
		"args": "src/page www/sitemap.xml"
	}
}
```

Not a dependency manager
------------------------

This library assumes the configuration of the system is already configured.

The primary objective is to provide a client-side build system that is automatically configured for PHP projects, leaving the configuration of the system down to the developer's choice of client-side dependency management software.

Features at a glance
--------------------

+ One-off builds
+ Background builds (watching the matching files and re-building where necessary)
+ Bring your own client-side dependency manager
