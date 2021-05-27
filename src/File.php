<?php

namespace Itwmw\Engine\Route\Ide\Helper;

/**
 * File reading class
 * @package W7\Engine\Core\Exceptions\Trace
 */
class File
{
    /**
     * The contents of the currently read file
     *
     * @var array
     */
    private $file;

    public function __construct(string $path)
    {
        $this->file = explode("\n", file_get_contents($path));
    }
    
    /**
     * Get the contents of the specified row
     *
     * @param int $line
     * @return string
     */
    public function readLine(int $line): string
    {
        return $this->file[$line - 1] ?? '';
    }

    /**
     * Find the location of a line of text in a file
     *
     * @param string $string
     * @return int If the search is successful, the number of rows is returned, otherwise -1 is returned.
     */
    public function findLine(string $string): int
    {
        $line = array_search($string, $this->file);
        if (is_numeric($line)) {
            return $line + 1;
        }
        return -1;
    }

    public function readContent(int $startLine, int $endLine)
    {
        $startLine--;
        $endLine--;
        if ($startLine < 0 || $startLine > count($this->file)) {
            throw new \RuntimeException('Start to cross the line');
        }

        if ($endLine < 0 || $endLine > count($this->file)) {
            throw new \RuntimeException('End to cross the line');
        }

        if ($endLine < $startLine) {
            throw new \RuntimeException('The ending line must not be smaller than the starting line');
        }

        $content = '';
        for ($i = $startLine; $i <= $endLine; $i++) {
            if (empty($content)) {
                $content = $this->file[$i];
            } else {
                $content .= "\n" . $this->file[$i];
            }
        }
        return $content;
    }
}
