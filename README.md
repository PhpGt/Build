Build system for PHP 7 projects.
================================

This project provides a system for defining and running client-side build processes automatically, using tools already installed by your favourite client-side dependency manager.

***

// TODO: Status badges.

Example usage
-------------

build.json:

```
{
	"src/script/**/*.js": {
		"name": "Babel transpile",
		"command": "./node_modules/.bin/babel src/script/main.js -o www/script.js",
		"requires": {
			"node": "^8.4",
			"@command": "^6.0"
		}
	},
	
	"src/style/**/*.scss": {
		"name": "Sass compilation",
		"command": "sass src/style/main.scss www/style.css",
		"requires": {
			"ruby": "2.4.2",
			"@command": "3.5.1"
		}
	},
	
	"src/page/**/*.{html|php}": {
		"name": "Siteman generation",
		"command": "php vendor/bin/sitemap www/sitemap.xml"
	}
}
```

Why
---

This library's primary objective is to provide a client-side build system that is automatically configured for the PHP projects. There are already so many well established client-side dependency managers that it would be a bad idea to introduce one built with PHP. Instead, the problem of dependency management is left to the developer's dependency manager of choice, and this library simply ensures the dependencies are in place, before running the commands.

Features at a glance
--------------------

+ One-off builds
+ Background builds (watching the matching files and re-building where necessary)
