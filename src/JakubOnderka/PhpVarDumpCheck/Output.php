<?php
namespace JakubOnderka\PhpVarDumpCheck;

/*
Copyright (c) 2012, Jakub Onderka
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies,
either expressed or implied, of the FreeBSD Project.
 */

class Output
{
    /** @var int */
    public $filesPerLine = 60;

    /** @var int */
    protected $checkedFiles;

    /** @var int */
    protected $totalFileCount;

    /** @var Writer\Writer */
    protected $writer;

    /**
     * @param Writer\Writer $writer
     */
    public function __construct(Writer\Writer $writer)
    {
        $this->writer = $writer ?: new Writer\Console;
    }

    public function ok()
    {
        $this->writer->write('.');
        $this->progress();
    }

    public function error()
    {
        $this->writer->write('X');
        $this->progress();
    }

    public function fail()
    {
        $this->writer->write('-');
        $this->progress();
    }

    /**
     * @param string|null $line
     */
    public function writeLine($line = null)
    {
        $this->writer->write($line . PHP_EOL);
    }

    /**
     * @param int $count
     */
    public function writeNewLine($count = 1)
    {
        $this->writer->write(str_repeat(PHP_EOL, $count));
    }

    /**
     * @param int $count
     */
    public function setTotalFileCount($count)
    {
        $this->totalFileCount = $count;
    }

    protected function progress()
    {
        if (++$this->checkedFiles % $this->filesPerLine === 0) {
            if ($this->totalFileCount != 0) { // !=
                $percent = round($this->checkedFiles / $this->totalFileCount * 100);
                $current = $this->stringWidth($this->checkedFiles, strlen($this->totalFileCount));
                $this->writeLine(" $current/$this->totalFileCount ($percent %)");
            }
        }
    }

    /**
     * @param string $input
     * @param int $width
     * @return string
     */
    protected function stringWidth($input, $width = 3)
    {
        $multiplier = $width - strlen($input);
        return str_repeat(' ', $multiplier > 0 ? $multiplier : 0) . $input;
    }
}