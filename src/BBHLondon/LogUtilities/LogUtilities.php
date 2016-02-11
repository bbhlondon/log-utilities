<?php

namespace BBHLondon\LogUtilities;
 
class LogUtilities {
    public $logs = [];

    public $findme;

    public $dateExpr = '/\d{2}\/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\/\d{4}:\d{2}:\d{2}:\d{2} (?:-|\+)\d{4}/';

    public $directories = [];

    public $ignore = ['.','..','.DS_Store', 'logreader.php', 'exports'];

    public $lines = [];

    public function __construct() {
    }

    public function processLog($log) {
        $handle = fopen($log, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $this->processLine($line);   
            }
            fclose($handle);
        } else {
            // error opening the file.
        } 
    }

    public function processLine($line) {
        $pos = strpos($line, $this->findme);

        if ($pos !== false && preg_match($this->dateExpr ,$line, $matches)) {
            $this->lines[date('U', strtotime($matches[0]))] = $line;
        }
    }

    public function createExportFolder($filename, $perms = 0755) {
        $dirname = dirname($filename);

        if ($dirname != '.' && ! file_exists($dirname)) {
            mkdir($dirname, $perms, true);
        }
    }


    public function find($findme)
    {
        $this->findme = $findme;
    }

    public function scanDir($dir) {
        if (is_dir($dir)) {
            $this->directories[] = $dir;

            $logs = scandir($dir);

            foreach ($logs as $log) {
                if (in_array($log, $this->ignore)) {
                    continue;
                }

                $file = $dir.'/'.$log;

                if ($this->isTextFile($file)) {
                    $this->logs[] = $file;
                }
            }
        } else {
            return false;
        }
    }

    public function processLogs() {
        foreach ($this->logs as $log) {
            $this->processLog($log);
        }
    }

    public function isTextFile($path) {
        if (mime_content_type($path) == 'text/plain') {
            return true;
        } else {
            return false;   
        }
    }

    public function scan() {
        $this->processLogs();
    }

    public function sortByDate() {
        ksort($this->lines);
    }

    public function output($newFilename = false) {
        if (!$newFilename) {
            $newFilename = 'log-'.date('U').'log.txt';
        }

        $this->createExportFolder($newFilename);

        $file = fopen($newFilename, "w");

        foreach ($this->lines as $line) {
            fwrite($file, $line);
        }

        fclose($file);
    }

    public function dateFrom($datetime) {
        // echo date('d/M/Y H:i:s O', strtotime($datetime))."<br />";
        $datatime = strtotime($datetime);
        foreach ($this->lines as $date => $line) {
            if (date('U', $date) < strtotime($datetime)) {
                unset($this->lines[$date]);
            }
        }
    }

    public function dateTo($datetime) {
        // echo date('d/M/Y H:i:s O', strtotime($datetime))."<br />";
        foreach ($this->lines as $date => $line) {
            if ($date > strtotime($datetime)) {
                unset($this->lines[$date]);
            }
        }
    }
}
