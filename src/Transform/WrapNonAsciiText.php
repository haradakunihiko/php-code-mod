<?php
namespace PHPCodeMod\Transform;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\Parser\Php7;
use PhpParser\NodeVisitor\CloningVisitor;

use PhpParser\PrettyPrinter\Standard;
use PHPCodeMod\Visitor\WrapNonAsciiTextVisitor;

use SplFileInfo;

class WrapNonAsciiText {

    /** @var  SplFileInfo */
    private $splInfo;

    function __construct(SplFileInfo $splInfo)
    {
        $this->splInfo = $splInfo;
    }

    public function transform(bool $dryRun = true) {

        $lexer = new Emulative([
            'usedAttributes' => [
                'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos',
            ],
        ]);

        $parser = new Php7($lexer);

        $statementPreservingTraverser = new NodeTraverser();
        $statementPreservingTraverser->addVisitor(new CloningVisitor());

        $oldStatements = $parser->parse(file_get_contents($this->splInfo));

        $newStatements = $statementPreservingTraverser->traverse($oldStatements);

        $visitor = new WrapNonAsciiTextVisitor($this->splInfo);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($visitor);
        $nodeTraverser->traverse($newStatements);

        foreach($visitor->getModifications() as $modification) {
            fputcsv(STDOUT, array(
                $this->splInfo->getPathname(),
                $modification['start_line'] ?? '',
                $modification['value'] ?? '',
            ));
        }

        if (!$dryRun) {
            $newCode = (new Standard)->printFormatPreserving($newStatements, $oldStatements, $lexer->getTokens());
            file_put_contents($this->splInfo->getRealPath(), $newCode);
        }
    }

}
