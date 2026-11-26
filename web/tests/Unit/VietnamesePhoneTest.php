<?php

use App\Rules\VietnamesePhone;
use Illuminate\Support\Facades\Validator;

it('accepts common vietnamese phone formats', function (string $phone) {
    expect(Validator::make(['phone' => $phone], ['phone' => new VietnamesePhone])->passes())->toBeTrue();
})->with(['0912 345 678', '0912345678', '+84912345678', '84 912 345 678', '0388.123.456', '070-123-4567']);

it('rejects invalid phone numbers', function (string $phone) {
    expect(Validator::make(['phone' => $phone], ['phone' => new VietnamesePhone])->passes())->toBeFalse();
})->with(['12345', '0212345678', 'abc', '09123456789']);

it('normalizes to the 0xxxxxxxxx form', function (string $phone) {
    expect(VietnamesePhone::normalize($phone))->toBe('0912345678');
})->with(['0912 345 678', '+84912345678', '84-912-345-678']);
