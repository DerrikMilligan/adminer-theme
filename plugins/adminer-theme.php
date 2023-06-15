<?php

/**
 * Add's support for custom themes, and a theme selector. Based upon Pematon's Admier theme.
 *
 * @link https://github.com/DerrikMilligan/adminer-theme
 * @link https://github.com/pematon/adminer-theme
 *
 * @author Derrik Milligan
 */
class AdminerTheme {

    const CSS_VERSION = 5;
    const ICONS_VERSION = 3;

    const CSS_DIR = __DIR__ . '/../css';

    /** @var string $theme_name */
    private $theme_name;

    /** @var bool $theme_selector */
    private $theme_selector;

    /** @var array $themes */
    private $themes;

    /**
     * Default theme and/or multiple theme names for given hosts can be specified in constructor.
     * File with theme name and .css extension should be located in css folder.
     *
     * @param string $default_theme Theme name of default theme.
     * @param bool   $theme_selector Whether or not the theme selector is available at login
     * @param array  $server_themes array(database-host => theme-name).
     */
    public function __construct(string $default_theme = "dark/Matrix", bool $theme_selector = true, array $server_themes = []) {
        define("PMTN_ADMINER_THEME", true);

        $this->theme_selector = $theme_selector;

        if ($theme_selector === true) {
            $this->themes = [
                'dark'  => array_map(fn($item) => $this->getThemeNameFromPath($item), glob(self::CSS_DIR.'/dark/*.css')),
                'light' => array_map(fn($item) => $this->getThemeNameFromPath($item), glob(self::CSS_DIR.'/light/*.css')),
            ];

            $this->savePostThemeToSession();
        }

        $this->theme_name = isset($_GET["username"]) && isset($_GET["server"]) && isset($server_themes[$_GET["server"]])
            ? $server_themes[$_GET["server"]]
            : ($_SESSION['theme'] ?? $default_theme);
        }

    /**
     * Add the theme field to the login form if desired
     */
    public function loginForm() {
        if ($this->theme_selector) {
            echo "<table cellspacing=\"0\" class=\"layout\">\n";
            echo "  <tr>\n";
            echo "    <th>Theme\n";
            echo "      <td>";
            echo          $this->buildThemeSelect();
            echo "      </td>\n";
            echo "    </th>\n";
            echo "  </tr>\n";
            echo "</table>\n";
        }
    }

    /**
     * Build the select from the theme names
     */
    private function buildThemeSelect(): string {
        $select = "<select name=\"theme\">\n";

        $pieces = explode('/', $_SESSION['theme']);

        foreach ($this->themes as $type => $themes) {
            $select .= "<optgroup label=\"".ucfirst($type)."\">\n";
            foreach ($themes as $theme_name) {
                $select .= "<option value=\"{$type}/{$theme_name}\"";

                if ($type === $pieces[0] && $theme_name === $pieces[1])
                    $select .= ' selected="selected"';

                $select .= ">{$theme_name}</option>\n";
            }
            $select .= "</optgroup>\n";
        }
        $select .= "</select>";
        return $select;
    }

    /**
     * Get the theme name from the path
     */
    private function getThemeNameFromPath(string $theme_path): string {
        $theme_name = basename($theme_path);
        $theme_name = substr($theme_name, 0, strlen($theme_name) - 4);
        return $theme_name;
    }

    /**
     * Save a theme to the session if it's a theme we've read out from the files
     */
    private function savePostThemeToSession() {
        if ($_POST['theme']) {
            $pieces = explode('/', $_POST['theme']);
            foreach($this->themes as $type => $themes) {
                foreach($themes as $theme) {
                    if ($type === $pieces[0] && $theme === $pieces[1]) {
                        $_SESSION['theme'] = $_POST['theme'];
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * Prints HTML code inside <head>.
     * @return false
     */
    public function head() {
        $userAgent = filter_input(INPUT_SERVER, "HTTP_USER_AGENT");
        $light_or_dark = explode('/', $this->theme_name)[0];
        ?>

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"/>

        <link rel="icon" type="image/ico" href="images/favicon.png">

        <?php
            // Condition for Windows Phone has to be the first, because IE11 contains also iPhone and Android keywords.
            if (strpos($userAgent, "Windows") !== false):
        ?>
            <meta name="application-name" content="Adminer"/>
            <meta name="msapplication-TileColor" content="#ffffff"/>
            <meta name="msapplication-square150x150logo" content="images/tileIcon.png"/>
            <meta name="msapplication-wide310x150logo" content="images/tileIcon-wide.png"/>

        <?php elseif (strpos($userAgent, "iPhone") !== false || strpos($userAgent, "iPad") !== false): ?>
            <link rel="apple-touch-icon-precomposed" href="images/touchIcon.png?<?php echo self::ICONS_VERSION ?>"/>

        <?php elseif (strpos($userAgent, "Android") !== false): ?>
            <link rel="apple-touch-icon-precomposed" href="images/touchIcon-android.png?<?php echo self::ICONS_VERSION ?>"/>

        <?php else: ?>
            <link rel="apple-touch-icon" href="images/touchIcon.png?<?php echo self::ICONS_VERSION ?>"/>
        <?php endif; ?>

        <link rel="stylesheet" type="text/css" href="css/theme-base.css?<?php echo self::CSS_VERSION ?>">
        <link rel="stylesheet" type="text/css" href="css/<?php echo htmlspecialchars($this->theme_name) ?>.css?<?php echo self::CSS_VERSION ?>">
        <link rel="stylesheet" type="text/css" href="css/theme-base-<?php echo $light_or_dark; ?>.css?<?php echo self::CSS_VERSION ?>">

        <script <?php echo nonce(); ?>>
            (function(document) {
                "use strict";

                document.addEventListener("DOMContentLoaded", init, false);

                function init() {
                    var menu = document.getElementById("menu");
                    var button = menu.getElementsByTagName("h1")[0];
                    if (!menu || !button) {
                        return;
                    }

                    button.addEventListener("click", function() {
                        if (menu.className.indexOf(" open") >= 0) {
                            menu.className = menu.className.replace(/ *open/, "");
                        } else {
                            menu.className += " open";
                        }
                    }, false);
                }

            })(document);

        </script>

        <?php

        // Return false to disable linking of adminer.css and original favicon.
        // Warning! This will stop executing head() function in all plugins defined after AdminerTheme.
        return false;
    }
}
