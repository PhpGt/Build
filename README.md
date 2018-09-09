Client-side build system for PHP projects.
==========================================

This project provides a system for defining and running client-side build processes automatically, using tools already installed by your favourite client-side dependency manager.

***

<a href="https://circleci.com/gh/PhpGt/Build" target="_blank">
	<img src="https://badge.status.php.gt/build-build.svg" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Build" target="_blank">
	<img src="https://badge.status.php.gt/build-quality.svg" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Build" target="_blank">
	<img src="https://badge.status.php.gt/build-coverage.svg" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/Build" target="_blank">
	<img src="https://badge.status.php.gt/build-version.svg" alt="Current version" />
</a>
<a href="http://www.php.gt/Build" target="_blank">
	<img src="https://badge.status.php.gt/build-docs.svg" alt="PHP.G/Build documentation" />
</a>

Example usage
-------------

build.json:

```json
{
	"src/script/**/*.js": {
		"name": "Babel transpile",
		"command": "./node_modules/.bin/babel",
		"args": "src/script/main.js -o www/script.js",
		"require": {
			"node": "^8.4",
			"@command": "^6.0"
		}
	},
	
	"src/style/**/*.scss": {
		"name": "Sass compilation",
		"command": "sass",
		"args": "src/style/main.scss www/style.css",
		"require": {
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
