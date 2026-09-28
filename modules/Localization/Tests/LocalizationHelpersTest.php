<?php

use Tests\TestCase;

uses(TestCase::class);

test('western digits are transliterated only for native digit locales', function () {
    expect(to_native_digits('Order 42', 'bn'))->toBe('Order ৪২')
        ->and(to_native_digits('Order 42', 'en'))->toBe('Order 42')
        ->and(to_native_digits('Order 42'))->toBe('Order 42');
});

test('native digits normalise back to western digits for form input', function () {
    expect(to_western_digits('১৪৫৬'))->toBe('1456')
        ->and(to_western_digits(to_native_digits('1234567890', 'bn')))->toBe('1234567890');
});

test('numbers keep western three digit grouping under every locale', function () {
    expect(format_number(1456))->toBe('1,456')
        ->and(format_number(1456.5, 2))->toBe('1,456.50');

    localization()->apply('bn');

    expect(format_number(1456))->toBe('১,৪৫৬')
        ->and(format_number(1456.5, 2))->toBe('১,৪৫৬.৫০');
});

test('money keeps the currency symbol in front of the digits', function () {
    expect(format_money(1456.5))->toBe('৳1,456.50');

    localization()->apply('bn');

    expect(format_money(1456.5))->toBe('৳১,৪৫৬.৫০');
});

test('english dates stay byte identical to php formatting', function () {
    expect(format_date('2024-03-05 14:30:00', 'd M Y, h:i A'))
        ->toBe('05 Mar 2024, 02:30 PM');
});

test('bangla dates translate the names and render native digits', function () {
    localization()->apply('bn');

    $formatted = format_date('2024-03-05 14:30:00', 'd M Y');

    expect($formatted)->toContain('মার্চ')
        ->and($formatted)->toContain('০৫')
        ->and($formatted)->toContain('২০২৪');
});

test('an empty date formats to an empty string', function () {
    expect(format_date(null))->toBe('')
        ->and(format_date(''))->toBe('');
});

test('document metadata follows the resolved locale', function () {
    expect(current_locale())->toBe('en')
        ->and(html_lang())->toBe('en')
        ->and(html_dir())->toBe('ltr')
        ->and(locale_uses_native_digits())->toBeFalse()
        ->and(locale_uses_native_digits('bn'))->toBeTrue();

    localization()->apply('bn');

    expect(current_locale())->toBe('bn')
        ->and(html_lang())->toBe('bn')
        ->and(html_dir())->toBe('ltr')
        ->and(locale_uses_native_digits())->toBeTrue();
});

test('only supported locales are ever advertised', function () {
    expect(supported_locales())->toHaveKeys(['en', 'bn'])
        ->and(localization()->codes())->toBe(['en', 'bn'])
        ->and(localization()->isSupported('fr'))->toBeFalse()
        ->and(localization()->isSupported('bn'))->toBeTrue();
});
