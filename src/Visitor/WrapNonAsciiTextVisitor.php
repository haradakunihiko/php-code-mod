<?php
namespace PHPCodeMod\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SplFileInfo;

/**
 * Wrap non ASCII text with a function
 *
 * Class MultiByteTextVisitor
 * @package traverser\visitor
 */
final class WrapNonAsciiTextVisitor extends NodeVisitorAbstract
{
    /** @var SplFileInfo  */
    private $splFileInfo;

    /** @var string */
    private $functionName;

    /** @var array */
    private $modifications = [];

    private $wrapNode;

    function __construct(\SplFileInfo $splFileInfo, string $functionName = '__')
    {
        $this->splFileInfo = $splFileInfo;
        $this->functionName = $functionName;
    }

    public function getModifications() {
        return $this->modifications;
    }

    public function enterNode(Node $node)
    {
        if ($this->wrapNode) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof Node\Expr\FuncCall) {
            if (isset($node->name->parts) && $node->name->parts === [$this->functionName]) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
        }

        if ($node instanceof Node\Scalar\String_) {
            if (!$this->isAscii($node->value)) {

                // log
                $this->modifications[] = [
                    'start_line' => $node->getStartLine(),
                    'value' => $node->value,
                ];

                $this->wrapNode = new Node\Expr\FuncCall(new Name('__'), [
                    new Node\Arg(new Node\Scalar\String_($node->value))
                ]);
                return $this->wrapNode;
            }
        }

        return $node;
    }

    public function leaveNode(Node $node)
    {
        if ($this->wrapNode === $node) {
            $this->wrapNode = null;
        }

        return parent::leaveNode($node);
    }

    private function isAscii($text)
    {
        return mb_check_encoding($text, 'ASCII');
    }
}
