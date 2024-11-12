<?php

namespace Path2URL;

use InvalidArgumentException;
use RuntimeException;
use Exception;

class Path2URL
{
    private string $directoryPath;
    private string $baseDomain;
    private array $supportedExtensions;
    private string $logFile;
    private bool $enableBackup;
    private array $processedFiles = [];

    public function __construct(
        string $directoryPath,
        string $baseDomain,
        array $supportedExtensions = ['html', 'css', 'js'],
        string $logFile = 'url_converter.log',
        bool $enableBackup = true
    ) {
        if (!is_dir($directoryPath) || !is_readable($directoryPath)) {
            throw new InvalidArgumentException("Invalid or unreadable directory: $directoryPath");
        }

        if (!filter_var($baseDomain, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid base domain URL: $baseDomain");
        }

        $this->directoryPath = rtrim($directoryPath, '/\\');
        $this->baseDomain = rtrim($baseDomain, '/');
        $this->supportedExtensions = $supportedExtensions;
        $this->logFile = $logFile;
        $this->enableBackup = $enableBackup;
    }

    public function process(): array
    {
        $stats = ['total' => 0, 'success' => 0, 'failed' => 0];
        $this->processedFiles = [];

        try {
            $files = $this->getFiles();
            $stats['total'] = count($files);

            foreach ($files as $file) {
                if ($this->processFile($file)) {
                    $stats['success']++;
                } else {
                    $stats['failed']++;
                }
            }

        } catch (Exception $e) {
            $this->log("Processing failed: " . $e->getMessage(), 'ERROR');
            throw new RuntimeException("Processing failed: " . $e->getMessage());
        }

        return $stats;
    }

    private function processFile(string $file): bool
    {
        try {
            if (!is_readable($file)) {
                throw new RuntimeException("File is not readable: $file");
            }

            $content = file_get_contents($file);
            if ($content === false) {
                throw new RuntimeException("Failed to read file: $file");
            }

            // Create backup before modifying
            if (!$this->backupFile($file)) {
                return false;
            }

            $relativeDirectory = trim(str_replace($this->directoryPath, '', dirname($file)), '/\\');
            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            $updatedContent = $this->convertUrlsToAbsolute($content, $relativeDirectory, $fileExtension);

            if (file_put_contents($file, $updatedContent) === false) {
                throw new RuntimeException("Failed to write to file: $file");
            }

            $this->processedFiles[] = $file;
            $this->log("Successfully processed file: $file");
            return true;

        } catch (Exception $e) {
            $this->log("Error processing file $file: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    private function convertUrlsToAbsolute(string $content, string $relativeDirectory, string $fileExtension): string
    {
        switch ($fileExtension) {
            case 'html':
                // Convert src and href attributes
                $content = preg_replace_callback(
                    '/(src|href)=(["\'])(\.{1,2}\/[^"\']+)\2/i',
                    function ($matches) use ($relativeDirectory) {
                        $absoluteUrl = $this->resolveAbsoluteUrl($matches[3], $relativeDirectory);
                        return $matches[1] . '=' . $matches[2] . $absoluteUrl . $matches[2];
                    },
                    $content
                );
                break;

            case 'css':
                // Convert url() references
                $content = preg_replace_callback(
                    '/url\((["\']?)(\.{1,2}\/[^)"\']+)\1\)/i',
                    function ($matches) use ($relativeDirectory) {
                        $absoluteUrl = $this->resolveAbsoluteUrl($matches[2], $relativeDirectory);
                        return 'url(' . $matches[1] . $absoluteUrl . $matches[1] . ')';
                    },
                    $content
                );
                break;
        }

        return $content;
    }

    private function resolveAbsoluteUrl(string $relativePath, string $relativeDirectory): string
    {
        // Remove leading ./ from the path
        $path = preg_replace('/^\.\//', '', $relativePath);

        // Split path into segments
        $pathSegments = explode('/', $path);
        $resultSegments = [];

        // Process relative directory if it exists
        if (!empty($relativeDirectory)) {
            $resultSegments = explode('/', $relativeDirectory);
        }

        // Process path segments
        foreach ($pathSegments as $segment) {
            if ($segment === '..') {
                array_pop($resultSegments);
            } elseif ($segment !== '.' && $segment !== '') {
                $resultSegments[] = $segment;
            }
        }

        // Build the final URL
        return $this->baseDomain . '/' . implode('/', $resultSegments);
    }

    private function backupFile(string $filePath): bool
    {
        // If backup is disabled, return true without doing backup
        if (!$this->enableBackup) {
            return true;
        }

        // Create backup directory if it doesn't exist
        $backupDir = dirname($this->directoryPath) . DIRECTORY_SEPARATOR . 'path2url_backup';
        if (!is_dir($backupDir) && !mkdir($backupDir, 0755, true)) {
            $this->log("Failed to create backup directory: $backupDir", 'ERROR');
            return false;
        }

        // Create backup file path maintaining the original directory structure
        $relativeFilePath = str_replace($this->directoryPath, '', $filePath);
        $backupFilePath = $backupDir . $relativeFilePath . '.' . time() . '.bak';
        
        // Create subdirectories if they don't exist
        $backupFileDir = dirname($backupFilePath);
        if (!is_dir($backupFileDir) && !mkdir($backupFileDir, 0755, true)) {
            $this->log("Failed to create backup subdirectory: $backupFileDir", 'ERROR');
            return false;
        }

        if (!copy($filePath, $backupFilePath)) {
            $this->log("Failed to create backup: $backupFilePath", 'ERROR');
            return false;
        }

        $this->log("Created backup: $backupFilePath");
        return true;
    }

    private function getFiles(): array
    {
        try {
            $files = [];
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->directoryPath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $fileExtension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                    if (in_array($fileExtension, $this->supportedExtensions)) {
                        $files[] = $file->getPathname();
                    }
                }
            }

            return $files;

        } catch (Exception $e) {
            throw new RuntimeException("Failed to scan directory: " . $e->getMessage());
        }
    }

    private function log(string $message, string $level = 'INFO'): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function getProcessedFiles(): array
    {
        return $this->processedFiles;
    }
}