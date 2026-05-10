<?php

declare(strict_types=1);

namespace App\Imports\Concerns;

/**
 * Normalizes row values from Excel before Laravel's WithValidation rules run.
 * PhpSpreadsheet often returns numeric types for cells formatted as numbers (e.g. student codes).
 */
trait PreparesStudentRowForValidation
{
    /**
     * @param array<string, mixed> $row
     * @param int|string $index
     * @return array<string, mixed>
     */
    public function prepareForValidation(array $row, $index): array
    {
        foreach (['ho', 'ten', 'email', 'ma_sinh_vien', 'so_dien_thoai'] as $field) {
            if (! array_key_exists($field, $row)) {
                continue;
            }

            $raw = $row[$field];
            if (null === $raw || '' === $raw) {
                continue;
            }

            if ('ma_sinh_vien' === $field && is_numeric($raw)) {
                $n = 0 + $raw;
                $asString = 0.0 === fmod((float) $n, 1.0)
                    ? (string) (int) $n
                    : (string) $n;
            } else {
                $asString = is_string($raw) ? $raw : (string) $raw;
            }

            $row[$field] = trim($asString);
        }

        return $row;
    }
}
