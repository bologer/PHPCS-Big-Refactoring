<?php

//namespace bologer\PhpcsParser;

class Parser
{
    private $_args = [];

    const ACTION_PARSE = 'parse';
    const ACTION_NEXT = 'next';


    private $_action;
    private $_path;

    const PARSED_FILE_NAME = 'parsed-output.json';


    public function __construct(array $args)
    {
        $this->_args = $args;

        $this->prepare();
    }


    private function prepare()
    {
        $args = $this->_args;

        $action = $args[1] ?? null;
        $path = $args[2] ?? null;

        if (empty($action)) {
            throw new \Exception('php parser.php <action>');
        }

        if ($action !== self::ACTION_PARSE && $action !== self::ACTION_NEXT) {
            throw new \Exception(sprintf(
                'Action must be either "%s" or "%s"',
                self::ACTION_PARSE,
                self::ACTION_NEXT
            ));
        }

        if ($action === self::ACTION_PARSE && empty($args[2])) {
            throw new \Exception(
                'Action "' . self::ACTION_PARSE . '" should have path to phpcs output to process it'
            );
        }

        $this->_action = trim($action);
        $this->_path = $path !== null ? trim($path) : null;
    }

    public function process()
    {
        switch ($this->_action) {
            case self::ACTION_PARSE:
                $fileContent = file_get_contents($this->_path);

                if (empty($fileContent)) {
                    throw new \Exception(sprintf('File %s is empty or has not read rights', $this->_path));
                }

                preg_match_all('/FILE: (\/.*?\.php)/m', $fileContent, $matches, PREG_SET_ORDER, 0);

                if (empty($matches)) {
                    throw new \Exception(sprintf(
                        'File %s does not have any phpcs errors',
                        $this->_path
                    ));
                }

                $cleanPaths = [];

                foreach ($matches as $match) {
                    $matchPath = $match[1] ?? null;

                    if ($matchPath !== null) {
                        $cleanPaths[] = $match[1];
                    }
                }

                if (@file_put_contents(self::PARSED_FILE_NAME, json_encode($cleanPaths))) {
                    echo '[OK] Parsed content located in ' . self::PARSED_FILE_NAME . PHP_EOL;
                }
                break;
            case self::ACTION_NEXT:

                $path = self::PARSED_FILE_NAME;

                if (!is_file($path)) {
                    echo '[ERR] You should first parse phpcs output via "php parser.php parse <absolute_path>' .
                        PHP_EOL;
                    exit(1);
                }

                $json = @file_get_contents(self::PARSED_FILE_NAME);

                if (empty($json)) {
                    echo '[ERR] File ' . $path . ' seems to be empty';
                    exit(1);
                }

                $array = json_decode($json);

                if (empty($array)) {
                    echo 'No more items';
                    exit(0);
                }

                shell_exec('pstorm ' . end($array));

                array_pop($array);

                file_put_contents($path, json_encode($array));

                break;

        }
    }
}

try {
    $parser = new Parser($argv);
    $parser->process();
} catch (\Exception $exception) {
    echo '[ERR] ' . $exception->getMessage() . PHP_EOL;
}
