<?php
// test_path2url.php

require_once __DIR__ . '/../src/Path2URL/Path2URL.php';

class Path2URLTester
{
    private $testDir;
    private $baseDomain = 'https://example.com';
    private $logFile;
    private $totalTests = 0;
    private $passedTests = 0;

    public function __construct()
    {
        // Create test directory
        $this->testDir = __DIR__ . '/test_files';
        $this->logFile = __DIR__ . '/url_converter.log';
        
        if (!file_exists($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }
    }

    private function assert($condition, $message)
    {
        $this->totalTests++;
        if ($condition) {
            echo "✅ PASS: $message\n";
            $this->passedTests++;
        } else {
            echo "❌ FAIL: $message\n";
        }
    }

    private function createTestFile($filename, $content)
    {
        $filepath = $this->testDir . '/' . $filename;
        file_put_contents($filepath, $content);
        return $filepath;
    }

    public function runTests()
    {
        echo "Starting Path2URL Tests...\n";
        echo "-------------------------\n\n";

        // Test 1: Constructor
        try {
            $converter = new Path2URL\Path2URL($this->testDir, $this->baseDomain);
            $this->assert(true, "Constructor works with valid inputs");
        } catch (Exception $e) {
            $this->assert(false, "Constructor failed: " . $e->getMessage());
        }

        // Test 2: HTML File Processing
        $htmlContent = '<img src="./images/test.jpg"><a href="../docs/file.pdf">';
        $htmlFile = $this->createTestFile('test.html', $htmlContent);
        
        $converter->process();
        $processedContent = file_get_contents($htmlFile);
        
        $this->assert(
            strpos($processedContent, 'src="https://example.com/images/test.jpg"') !== false,
            "HTML relative image path converted correctly"
        );
        $this->assert(
            strpos($processedContent, 'href="https://example.com/docs/file.pdf"') !== false,
            "HTML relative link path converted correctly"
        );

        // Test 3: CSS File Processing
        $cssContent = 'background-image: url("./images/bg.jpg"); background: url("../assets/pattern.png");';
        $cssFile = $this->createTestFile('styles.css', $cssContent);
        
        $converter->process();
        $processedContent = file_get_contents($cssFile);
        
        $this->assert(
            strpos($processedContent, 'url("https://example.com/images/bg.jpg")') !== false,
            "CSS relative image path converted correctly"
        );
        $this->assert(
            strpos($processedContent, 'url("https://example.com/assets/pattern.png")') !== false,
            "CSS relative pattern path converted correctly"
        );

        // Test 4: Backup Creation
        $backupFiles = glob($htmlFile . '.*.bak');
        $this->assert(
            !empty($backupFiles),
            "Backup files created successfully"
        );

        // Test 5: Invalid Directory Test
        try {
            new Path2URL\Path2URL('/nonexistent/directory', $this->baseDomain);
            $this->assert(false, "Should fail with invalid directory");
        } catch (InvalidArgumentException $e) {
            $this->assert(true, "Correctly handles invalid directory");
        }

        // Test 6: Invalid Domain Test
        try {
            new Path2URL\Path2URL($this->testDir, 'invalid-domain');
            $this->assert(false, "Should fail with invalid domain");
        } catch (InvalidArgumentException $e) {
            $this->assert(true, "Correctly handles invalid domain");
        }

        // Display Results
        echo "\nTest Results:\n";
        echo "------------\n";
        echo "Total Tests: {$this->totalTests}\n";
        echo "Passed: {$this->passedTests}\n";
        echo "Failed: " . ($this->totalTests - $this->passedTests) . "\n";
    }

    public function cleanup()
    {
        // Clean up test files
        if (file_exists($this->testDir)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->testDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                if ($fileinfo->isDir()) {
                    rmdir($fileinfo->getRealPath());
                } else {
                    unlink($fileinfo->getRealPath());
                }
            }
            rmdir($this->testDir);
        }

        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }
}

// Run the tests
try {
    $tester = new Path2URLTester();
    $tester->runTests();
    $tester->cleanup();
} catch (Exception $e) {
    echo "Test execution failed: " . $e->getMessage() . "\n";
}