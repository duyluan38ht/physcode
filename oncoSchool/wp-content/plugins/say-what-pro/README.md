# Say what? Pro
An easy-to-use plugin that allows you to alter strings on your site without editing WordPress core, or plugin code. Simply enter the current string, and what you want to replace it with and the plugin will automatically do the rest! The Pro version includes automated string discovery to make it easier for you to find the strings you need to override.

### Installation
* Install it as you would any other plugin
* Activate it
* Head over to Tools &raquo; Text changes and configure some string replacements - or activate "String discovery" to make it easier to find the strings you want.

### Frequently Asked Questions

#### Can I use it to change any string?
You can only use the plugin to translate strings which are marked for translation.

#### How do I find the string to translate?
The plugin has a "String Discovery" mode which will capture the strings available for replacement, and let you choose them via an autocomplete system when adding a replacement. Check out the "String Discovery" tab in the plugin settings.

### WP-CLI Support

"Say What?" has preliminary support for exporting, and importing replacements via [http://wp-cli.org/](WP-CLI). The following commands are currently
supported:
* export - Export all current string replacements.
* import - Import string replacements from a CSV file.
* list - Export all current string replacements. Synonym for 'export'.
* update - update string replacements from a CSV file.

Examples:
```
$ wp say-what export
+-----------+-------------+--------+--------------------+---------+
| string_id | orig_string | domain | replacement_string | context |
+-----------+-------------+--------+--------------------+---------+
| 3         | Tools       |        | Yada dada tools!   |         |
+-----------+-------------+--------+--------------------+---------+
```

```
$ wp say-what import import-file.csv
Success: 27 new items created.
```

```
$ wp say-what update update-file.csv
Success: 14 records updated, 19 new items created.
```

### Changelog

### 2.6.0
Fix: Replacements sometimes didn't pick up correct locale when using polylang

### 2.5.4
New: When importing replacements, unrecognised IDs will be treated as INSERTs, not ignored
 
### 2.5.3
New: Add settings link on WordPress plugins page

#### 2.5.2
Fix: PHP minimum version checks didn't always fire correctly.
New: Online imports can now do insert or replace if ID provided
New: Include POT file for easy translation of the plugin

#### 2.5.1 
Better autocomplete admin behaviour when no matches / suggestions found
Internal reworking, and test improvements

#### 2.5.0
Allow searching for strings in string discovery by translated text, not just original

#### 2.4.1
User-interface cleanups to admin screens

#### 2.4.0
If using multi-lingual, sort active languages to the top of the language list.

#### 2.3.0
Add Wildcard Swaps feature
Add delete bulk actions to list tables

#### 2.2.7
Enable multi-lingual features if WeGlot is active

#### 2.2.6
Re-package 2.2.5 properly.

#### 2.2.5
Better behaviour when activating the Pro plugin while the free version is still active

#### 2.2.4
Fix importer issues with odd Windows mime types
Fix issue where autocomplete suggestions that differed in case would only show one version

#### 2.2.3
Fix issue where imports could error in some setups.

#### 2.2.2
Fix issue where exports would give "Headers already sent" errors.

#### 2.2.1
Fix issue where "English (United States)" isn't available as a choice when setting up multi-lingual strings.

#### 2.2
Fix performance issues identified under some configurations with v2.1

#### 2.1
Allow different replacements to be set for different languages.

#### 2.0.1
Fix issue with occasional double-encoding on admin screens.

#### 2.0
Introduce import/export feature
Requires PHP 5.3 or higher

#### 1.9
Fixes, and better support for multi-line strings.

#### 1.8
UI fixes in admin area for RTL languages

#### 1.7
Support for pluralised translations (E.g. _n(), and _nx()).

#### 1.6
Remove extra output on plugin admin page.

#### 1.5
Introduce filter that allows back compatibility for plugins that change their text-domain. Props Pippin Williamson.

#### 1.4
Responsive table support for WordPress 4.3
Clearer, more consistent row actions on admin table.

#### 1.3
Fix issues replacing strings containing HTML / HTML entities.


#### 1.2
Fix autocomplete for sites running in subfolders.
Avoid duplicates in the available table being returned in autocomplete suggestions.

#### 1.1
Resolve String Discovery issues on older MySQL releases.

#### 1.0
Initial release of Pro version.
