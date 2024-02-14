<?php
namespace App\Function;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Geo extends FunctionNode
{
    public $latitude;
    public $longitude;
    public $pointLatitude;
    public $pointLongitude;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->latitude = $parser->ArithmeticPrimary(); // latitude of starting point
        $parser->match(Lexer::T_COMMA);
        $this->longitude = $parser->ArithmeticPrimary(); // longitude of starting point
        $parser->match(Lexer::T_COMMA);
        $this->pointLatitude = $parser->ArithmeticPrimary(); // latitude of target point
        $parser->match(Lexer::T_COMMA);
        $this->pointLongitude = $parser->ArithmeticPrimary(); // longitude of target point
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            '(6366 * acos(cos(radians(CAST(%s AS DOUBLE PRECISION))) * cos(radians(CAST(%s AS DOUBLE PRECISION))) * cos(radians(CAST(%s AS DOUBLE PRECISION)) - radians(CAST(%s AS DOUBLE PRECISION))) + sin(radians(CAST(%s AS DOUBLE PRECISION))) * sin(radians(CAST(%s AS DOUBLE PRECISION)))))',
            $this->latitude->dispatch($sqlWalker),
            $this->pointLatitude->dispatch($sqlWalker),
            $this->pointLongitude->dispatch($sqlWalker),
            $this->longitude->dispatch($sqlWalker),
            $this->latitude->dispatch($sqlWalker),
            $this->pointLatitude->dispatch($sqlWalker)
        );
    }
    
}
