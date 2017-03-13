<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Functions\Platform\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Functions\Cast as DqlFunction;

/**
 * Class Cast.
 */
class Cast
{
    /**
     * @var array
     */
    public $parameters;
    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $value */
        $value = $this->parameters[DqlFunction::PARAMETER_KEY];
        $type = $this->parameters[DqlFunction::TYPE_KEY];
        $type = strtolower($type);
        $isBoolean = $type === 'bool' || $type === 'boolean';
        if ($type === 'char') {
            $type = 'char(1)';
        } elseif ($type === 'string' || $type === 'text' || $type === 'json') {
            $type = 'char';
        } elseif ($type === 'int' || $type === 'integer' || $isBoolean) {
            $type = 'signed';
        }
        $expression = 'CAST('.$this->getExpressionValue($value, $sqlWalker).' AS '.$type.')';
        if ($isBoolean) {
            $expression .= ' <> 0';
        }

        return $expression;
    }

    /**
     * Get expression value string.
     *
     * @param string|Node $expression
     * @param SqlWalker   $sqlWalker
     *
     * @return string
     */
    protected function getExpressionValue($expression, SqlWalker $sqlWalker)
    {
        if ($expression instanceof Node) {
            $expression = $expression->dispatch($sqlWalker);
        }

        return $expression;
    }
}
