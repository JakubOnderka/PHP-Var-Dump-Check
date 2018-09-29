<?php
namespace JakubOnderka\PhpVarDumpCheck;

class Manager
{
    /**
     * @param Settings $settings
     * @param Output|null $output
     * @return bool
     * @throws Exception\NotExistsPath
     */
    public function check(Settings $settings, Output $output = null)
    {
        $files = $this->getFilesFromPaths($settings->paths, $settings->extensions, $settings->excluded);
        $checkedFiles = count($files);

        $output = $output ?: ($settings->colors ? new OutputColored(new Writer\Console) : new Output(new Writer\Console));
        $output->setTotalFileCount($checkedFiles);

        /** @var Result[] $results */
        $results = array();

        $startTime = microtime(true);
        $checkedFiles = 0;
        $filesWithDump = 0;

        foreach ($files as $file) {
            try {
                $fileResult = $this->checkFile($file, $settings);
                $checkedFiles++;

                if (empty($fileResult)) {
                    $output->ok();
                } else {
                    $output->error();
                    $filesWithDump++;
                }

                $results = array_merge($results, $fileResult);
            } catch (Exception\Exception $e) {
                $output->fail();
            }
        }

        $runTime = round(microtime(true) - $startTime, 1);

        $output->writeNewLine(2);

        $message = "Checked $checkedFiles files in $runTime second, ";
        if ($filesWithDump === 0) {
            $message .= "no dump found.";
        } else {
            $message .= "dump found in $filesWithDump ";
            $message .= ($filesWithDump === 1 ? 'file' : 'files');
        }

        $output->writeLine($message);

        if (!empty($results)) {
            $output->writeNewLine();

            foreach ($results as $result) {
                $output->writeLine(str_repeat('-', 60));
                $output->writeResult($result);
            }

            return false;
        }


        return true;
    }

    /**
     * @param string $filename
     * @param Settings $settings
     * @return Result[]
     * @throws Exception\FileNotFound
     * @throws Exception\FileOpen
     */
    public function checkFile($filename, Settings $settings = null)
    {
        if ($settings === null) {
            $settings = new Settings();
        }

        $content = $this->loadFile($filename);

        $checker = new Checker($settings);
        $results = $checker->check($content);
        $this->setFilePathToResults($results, $filename);

        return $results;
    }

    /**
     * @param string $filename
     * @return string
     * @throws Exception\FileOpen
     * @throws Exception\FileNotFound
     */
    protected function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception\FileNotFound("File '$filename' was not found.'");
        }

        $content = file_get_contents($filename);

        if ($content === false) {
            throw new Exception\FileOpen("Can not open file '$filename'.");
        }

        return $content;
    }

    /**
     * @param array $paths
     * @param array $extensions
     * @param array $excluded
     * @return array
     * @throws Exception\NotExistsPath
     */
    protected function getFilesFromPaths(array $paths, array $extensions, array $excluded = array())
    {
        $extensions = array_flip($extensions);
        $files = array();

        foreach ($paths as $path) {
            if (is_file($path)) {
                $files[] = $path;
            } else if (is_dir($path)) {
                $iterator = new \RecursiveDirectoryIterator($path);
                if (!empty($excluded)) {
                    $iterator = new RecursiveDirectoryFilterIterator($iterator, $excluded);
                }
                $iterator = new \RecursiveIteratorIterator($iterator);

                /** @var \SplFileInfo[] $iterator */
                foreach ($iterator as $directoryFile) {
                    if (isset($extensions[pathinfo($directoryFile->getFilename(), PATHINFO_EXTENSION)])) {
                        $files[] = (string) $directoryFile;
                    }
                }
            } else {
                throw new Exception\NotExistsPath($path);
            }
        }

        return $files;
    }

    /**
     * @param Result[] $results
     * @param string $filePath
     */
    protected function setFilePathToResults(array $results, $filePath)
    {
        foreach ($results as $result) {
            $result->setFilePath($filePath);
        }
    }
}