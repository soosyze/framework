<?php

use Soosyze\Components\Router\Route;

Route::useNamespace('Soosyze\Test');
Route::get('test', 'index', 'TestModule@index');
Route::get('test.json', 'json', 'TestModule@api');
