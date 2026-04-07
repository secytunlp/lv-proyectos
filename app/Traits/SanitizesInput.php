<?php

namespace App\Traits;

trait SanitizesInput
{
    public function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeValue'], $input);
        }

        return $this->sanitizeValue($input);
    }

    protected function sanitizeValue($value)
    {
        if (is_string($value)) {
            $value = trim($value);

            // Escapa < cuando no es HTML real
            $value = preg_replace('/<(?=\d)/', '&lt;', $value);

            return strip_tags($value, '<p><br><strong><em><ul><ol><li><a><h1><h2><h3>');
        }

        return $value;
    }
}
