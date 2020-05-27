# amos-sondaggi

Plugin to make surveys.

## Installation

### 1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
composer require open20/amos-sondaggi
```

or add this row

```
"open20/amos-sondaggi": "~1.0"
```

to the require section of your `composer.json` file.


### 2. Add module to your main config in backend:
	
```php
<?php
'modules' => [
    'sondaggi' => [
        'class' => 'open20\amos\sondaggi\AmosSondaggi'
    ],
],
```


### 3. Apply migrations

```bash
php yii migrate/up --migrationPath=@vendor/open20/amos-sondaggi/src/migrations
```

or add this row to your migrations config in console:

```php
<?php
return [
    '@vendor/open20/amos-sondaggi/src/migrations',
];
```
