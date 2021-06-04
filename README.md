# Search & Replace Bundle for Pimcore
Add the option to search and replace text in all pimcore documents.

Note that this bundle is quite new and has not been tested thoroughly. 
Use at your own risk! No warranty given. 
This bundle does directly modify the Pimcore Database tables. Backups are highly recommended.

## Features
* Easy installation in pimcore projects (drop-in-solution)
* Multilingual admin interface to search and replace
* Testrun with replacement preview

![Backend Interface](docs/img/search_form.png)

![Backend Interface](docs/img/replacement_preview.png)

## Installation

### Composer Installation
1. Add code below to your `composer.json` or install it via command line

```json
"require": {
    "pringuin/searchreplacebundle" : "dev-main"
}
```

### Installation via Extension Manager
After you have installed the Search & Replace Bundle via composer, open the pimcore administration backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

### Installation via CommandLine
After you have installed the Search & Replace Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:enable pringuinSearchreplaceBundle`
- Execute: `$ bin/console pimcore:bundle:install pringuinSearchreplaceBundle`

## Contributing
We'd be very happy if you'd support us by improving this bundle with pull requests.

## Copyright and license
Copyright: [PRinguin GbR](https://pringuin.de)  
For licensing details please visit [LICENSE.md](LICENSE.md)  
