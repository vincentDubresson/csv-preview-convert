<?php

namespace VdubDev\CsvPreviewConvert\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CsvPreviewConvertManager.
 *
 * Service responsible for managing temporary CSV files used in the "preview" feature.
 *
 * Main responsibilities:
 * - Handle the upload of CSV files by saving them into a temporary directory
 * - Ensure the temporary directory exists and is writable
 * - Generate previews of CSV files with configurable encoding, separator, and line limit
 * - Convert the character encoding of uploaded files without altering the originals
 * - Clean up outdated temporary files based on a configurable lifetime
 *
 * Typical usage scenario:
 * - A user uploads a CSV file for preview
 * - The service saves it into a temporary location
 * - The service provides a preview of the first N lines with the chosen separator
 * - If necessary, the file encoding can be converted for correct display
 * - Old files are periodically removed to keep the temporary storage clean
 */
class CsvPreviewConvertManager
{
    private string $tmpDir;
    private int $lifetime;

    public function __construct(string $tmpDir, int $lifetime)
    {
        $this->tmpDir = $tmpDir;
        $this->lifetime = $lifetime;

        // Ensure the temporary directory exists
        // Create it recursively with 0777 permissions if it does not exist
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }
    }

    /**
     * Save an uploaded CSV file into the temporary directory.
     *
     * This method preserves the original filename while adding a unique ID
     * to avoid collisions. The file is moved to the temporary directory
     * defined in the service.
     *
     * Example:
     * Original filename: clients.csv
     * Saved filename: clients_64fcd1234a5b6.csv
     */
    public function saveUploadedFile(UploadedFile $file): string
    {
        $originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        $extension = 'csv';

        $filename = $originalName . '_' . uniqid() . '.' . $extension;

        $filePath = $this->tmpDir . '/' . $filename;

        $file->move($this->tmpDir, $filename);

        return $filePath;
    }

    /**
     * Generate a preview of a CSV file using configurable encoding and delimiter options.
     *
     * This method reads the given file, extracts a limited number of lines,
     * and parses them into arrays of columns.
     *
     * @return array{
     *     success: bool,
     *     preview_data: array<int, array{
     *         line_number: int,
     *         display_line: string[],
     *     }>,
     *     error: string|null,
     * }
     *
     * Example return value:
     * [
     *     "success" => true,
     *     "preview_data" => [
     *         [
     *             "line_number" => 1,
     *             "display_line" => ["Name", "Age", "City"]
     *         ],
     *         [
     *             "line_number" => 2,
     *             "display_line" => ["Alice", "30", "Paris"]
     *         ]
     *     ],
     *     "error" => null,
     * ]
     */
    public function previewFile(string $filePath, ?string $separator = ';', ?int $previewLines = 100): array
    {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'preview_data' => [],
                'error' => 'File not found',
            ];
        }

        $rawContent = file_get_contents($filePath);

        if ($rawContent === false) {
            return [
                'success' => false,
                'preview_data' => [],
                'error' => 'Unable to read the file',
            ];
        }

        $lines = explode("\n", $rawContent);

        $previewLines = min($previewLines, 500);
        $lines = array_slice($lines, 0, $previewLines);

        $parsedLines = [];

        foreach ($lines as $lineIndex => $line) {
            if (trim($line) === '') {
                continue;
            }

            $sep = $separator ?: ';';
            $convertedLine = explode($sep, $line);

            $parsedLines[] = [
                'line_number' => $lineIndex + 1,
                'display_line' => $convertedLine,
            ];
        }

        return [
            'success' => true,
            'preview_data' => $parsedLines,
            'error' => null,
        ];
    }

    /**
     * Converts the character encoding of a file's content.
     *
     * This method reads the entire content of the file located at the given path
     * and converts its encoding from the specified source encoding to the target encoding.
     * It returns the converted content as a string without modifying the original file.
     */
    public function convertEncoding(string $filePath, string $fromEncoding, string $toEncoding): string
    {
        // Lire le contenu du fichier
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Impossible de lire le fichier : $filePath");
        }

        // Convertir l'encodage
        return (string) mb_convert_encoding($content, $toEncoding, $fromEncoding);
    }

    /**
     * Clean old temporary files.
     *
     * This method removes CSV files from the temporary directory
     * if their last modification time is older than the configured lifetime.
     *
     * Example: if $lifetime = 1800 (30 minutes),
     * any file older than 30 minutes will be deleted.
     */
    public function cleanOldFiles(): void
    {
        $files = glob($this->tmpDir . '/*.csv');

        if ($files === false) {
            // No files found or an error occurred
            return;
        }

        foreach ($files as $file) {
            if (filemtime($file) < (time() - $this->lifetime)) {
                @unlink($file);
            }
        }
    }
}
