<?php
require_once "vendor/autoload.php";
require_once "Transform/WrapNonAsciiText.php";
require_once "Visitor/WrapNonAsciiTextVisitor.php";

use PHPCodeMod\Transform\WrapNonAsciiText;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeDumper;
use PhpParser\Parser\Php7;

function wrapNonAsciiText(string $dir, bool $dryRun)
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir,
            FilesystemIterator::CURRENT_AS_FILEINFO |
            FilesystemIterator::KEY_AS_PATHNAME |
            FilesystemIterator::SKIP_DOTS
        )
    );

    /** @var SplFileInfo $fileinfo */
    foreach ($iterator as $fileinfo) {
        (new WrapNonAsciiText($fileinfo))->transform($dryRun);
    }
}

//wrapNonAsciiText('/path/to/directory', false);

function dumpAST($filePath)
{
    $lexer = new Emulative([
        'usedAttributes' => [
            'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos',
        ],
    ]);

    $node = (new Php7($lexer))->parse(file_get_contents(new SplFileInfo($filePath)));
    echo (new NodeDumper())->dump($node) . PHP_EOL;
}

//dumpAST('/path/to/file');
