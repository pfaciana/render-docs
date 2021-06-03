# Render Docs
Take output json files from npm package `parse-php-to-json` and render the docs as readable html pages.

## Example Use

### Install

```bash
composer require pfaciana/render-docs
```

### Run

Place this code in on your web server. In it's simpliest form, you just need to call `RenderDocs()` and tell it where to find your JSON files (outputted from `parse-php-to-json`). Then view this page in your web browser.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

RenderDocs([
    'docRoot'   => __DIR__ . '/../docs/', // Required. Location of the JSON files (Projects grouped as sub-directories) 
    'editorUrl' => 'http://localhost:1234/api/file/', // Optional. If you have an IDE installed, this will prefix a link to open the file (if it exists locally) in your IDE. 
    'locationFilter' => function ($file) { /* alter filepath */ return $file; }, // Optional. Used with `editorUrl`, if the file exists in a different location locally than in the repo, this is a filter that allows you to alter the filepath so that you can link to it locally. 
]);

```
