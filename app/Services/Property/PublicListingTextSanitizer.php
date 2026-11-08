<?php

namespace App\Services\Property;

use App\Models\Property;

final class PublicListingTextSanitizer
{
    public function clean(Property $property, ?string $text): ?string
    {
        if ($text === null) return null;
        $street = trim((string) data_get($property->address, 'line_1'));
        if ($street === '') return trim($text);
        $cleaned = preg_replace('/'.preg_quote($street, '/').'/iu', '', $text) ?? $text;
        return trim(preg_replace('/\s{2,}/', ' ', $cleaned));
    }
}
