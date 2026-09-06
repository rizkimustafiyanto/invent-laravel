<?php

namespace App\Modules\Shared\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvHelper
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public static function download(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'wb');

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function read(string $path): array
    {
        $handle = fopen($path, 'rb');

        if (! $handle) {
            return [];
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }

            $rows[] = array_map(static fn ($value) => is_string($value) ? trim($value) : '', $row);
        }

        fclose($handle);

        return $rows;
    }
}
