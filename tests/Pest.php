<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Hacemos que tanto los tests de Feature como los de Unit usen el TestCase
| de Laravel, así el framework y la DB se inicializan en ambos.
|
*/

uses(TestCase::class)->in('Feature', 'Unit');
// Si quisieras forzar RefreshDatabase en TODO el proyecto, descomenta esta línea,
// pero normalmente es mejor declararlo por archivo.
// uses(Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function something()
{
    // helpers globales para tus tests (opcional)
}
