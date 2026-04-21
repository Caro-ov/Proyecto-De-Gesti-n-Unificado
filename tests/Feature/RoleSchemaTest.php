<?php

use Illuminate\Support\Facades\Schema;

test('roles schema matches the current role and user models', function () {
    expect(Schema::hasTable('roles'))->toBeTrue();
    expect(Schema::hasColumns('roles', ['name', 'description', 'status']))->toBeTrue();
    expect(Schema::hasColumns('users', ['role', 'status']))->toBeTrue();
});
