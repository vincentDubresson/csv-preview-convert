<?php

namespace VdubDev\CsvPreviewConvert\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Manages the storage and validation of the current CSV file path in the user's session.
 *
 * This service provides methods to set, get, clear, and validate the CSV file path
 * stored in the session. It ensures that any path retrieved from the session resides
 * within a specified temporary directory to prevent path traversal vulnerabilities.
 */
class CsvPreviewConvertSessionManager
{
    private RequestStack $requestStack;
    private string $tmpDir;

    /**
     * Session key to store current CSV path.
     */
    private const SESSION_KEY = 'csv_preview_convert_current_csv_file';

    public function __construct(RequestStack $requestStack, string $tmpDir)
    {
        // Get the session from the current request
        $this->requestStack = $requestStack;
        $this->tmpDir = $tmpDir;
    }

    /**
     * Store the CSV file path in session.
     */
    public function setCurrentCsvPath(string $filePath): void
    {
        $this->getSession()->set(self::SESSION_KEY, $filePath);
    }

    /**
     * Get the CSV file path from session.
     */
    public function getCurrentCsvPath(): ?string
    {
        /** @var ?string $currentCsvPath */
        $currentCsvPath = $this->getSession()->get(self::SESSION_KEY);

        return $currentCsvPath;
    }

    /**
     * Remove the CSV file path from session.
     */
    public function clearCurrentCsvPath(): void
    {
        $this->getSession()->remove(self::SESSION_KEY);
    }

    /**
     * Checks whether a valid CSV file exists in the session.
     *
     * This method validates that:
     * - A CSV path is stored in the session.
     * - The resolved absolute path exists on the filesystem.
     * - The path resides within the configured temporary directory to prevent path traversal.
     */
    public function hasValidCsv(): bool
    {
        $path = $this->getCurrentCsvPath();

        if ($path === null) {
            return false;
        }

        $realPath = realpath($path);
        if ($realPath === false) {
            return false;
        }

        if (!str_starts_with($realPath, $this->tmpDir)) {
            return false;
        }

        return file_exists($realPath);
    }

    /**
     * Retrieves the current session from the RequestStack.
     */
    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
