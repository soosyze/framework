<?php

use Soosyze\Components\Router\Route;

Route::useNamespace('Soosyze\Tests\Resources\App');
Route::get('test', 'index', 'TestController@index');
Route::get('test.json', 'json', 'TestController@getApi');
