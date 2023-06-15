Theme for Adminer
=================

Responsive touch-friendly theme for Adminer database tool ([www.adminer.org](http://www.adminer.org/)).

Based upon [pematon's adminer themes](https://github.com/pematon/adminer-theme).

## Differences
- Tweaked the theme to use CSS variables allowing for easy theme creation
- Added a theme picker at the login screen to allow selecting themes.

## Compatibility
Minimal requirements are: PHP 5.4, Adminer 4.6.1, modern web browser.

## How to use

1. [Download](http://www.adminer.org/#download) and install Adminer tool.

2. Download all content from /lib folder right next to adminer.php.

File structure will be:
```
- css
- fonts
- images
- plugins
- adminer.php
```

3. Create index.php file and [configure plugins](http://www.adminer.org/plugins/#use). Don't forget to copy official [plugin.php](https://raw.githubusercontent.com/vrana/adminer/master/plugins/plugin.php) into the `plugins` folder.

```php
<?php

	function adminer_object()
	{
		// Required to run any plugin.
		include_once "./plugins/plugin.php";

		// Plugins auto-loader.
		foreach (glob("plugins/*.php") as $filename) {
			include_once "./$filename";
		}

		// Specify enabled plugins here.
		$plugins = [
			// AdminerTheme has to be the last one!
			new AdminerTheme(),
			
			// Color variant can by specified in constructor parameter.
			// new AdminerTheme("dark/Matrix"),
			// new AdminerTheme("light/Blue"),
			// new AdminerTheme("dark/Red", true, ["192.168.0.1" => "dark/Matrix"]),
		];

		return new AdminerPlugin($plugins);
	}

	// Include original Adminer or Adminer Editor.
	include "./adminer.php";
```

Final file structure will be:
```
- css
- fonts
- images
- plugins
	- AdminerTheme.php
	- plugin.php
- adminer.php
- index.php
```

## Themes
Creating a new theme is a fairly straightforward process.

Copy a light or dark theme file and pick a couple of new colors! One for the background and one for the highlighted text! Check [here](https://colorhunt.co) for some inspiration if you need.

Convert those colors to HSL, [here](https://htmlcolors.com/hex-to-hsl) is an online tool you can use.

And in your new theme you'll update these CSS variables:

```css
:root {
  /* Yacht Club Dark theme */
  /* Split up the HSL color so we can use math to ligthen/darken the colors */
  --background-color-HS: 199, 87%;
  --background-color-L: 12%;

  --text-color-primary-HS: 35, 78%;
  --text-color-primary-L: 66%;
}
```

**NOTICE** that the each color is split across two variables. This is so that we can do some math with the `Lightness` portion of the HSL color and you don't have to pick more colors.

And that's it! It should show up in the dropdown now and you can select it!


## References
Amazing **Entypo** pictograms are created by Daniel Bruce ([www.entypo.com](http://www.entypo.com/)).
