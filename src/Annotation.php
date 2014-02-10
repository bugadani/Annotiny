<?php

/**
 * This file is part of the Miny framework.
 * (c) Dániel Buga <bugadani@gmail.com>
 *
 * For licensing information see the LICENSE file.
 */

namespace Modules\Annotation;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Annotation reads and parses annotations from documentation comments.
 *
 * @author Dániel Buga <bugadani@gmail.com>
 */
class Annotation
{
    /**
     * @var Annotation
     */
    private $parser;

    /**
     * @var CommentFactory
     */
    private $factory;

    /**
     * @param AnnotationParser $parser
     * @param CommentFactory $factory
     */
    public function __construct(AnnotationParser $parser, CommentFactory $factory)
    {
        $this->parser  = $parser;
        $this->factory = $factory;
    }

    /**
     * @param string $comment
     * @return array
     */
    protected function parseComment($comment)
    {
        return $this->parser->parse($comment);
    }

    /**
     * @param Reflector $reflector
     * @return Comment
     */
    protected function process(Reflector $reflector)
    {
        $comment = $reflector->getDocComment();
        $parsed  = $this->parseComment($comment);
        return $this->factory->create($parsed['description'], $parsed['tags']);
    }

    /**
     * Reads and parses documentation comments from classes.
     *
     * @param string|object $class
     * @return Comment
     */
    public function readClass($class)
    {
        return $this->process(new ReflectionClass($class));
    }

    /**
     * Reads and parses documentation comments from functions.
     *
     * @param string|Closure $function
     * @return Comment
     */
    public function readFunction($function)
    {
        return $this->process(new ReflectionFunction($function));
    }

    /**
     * Reads and parses documentation comments from methods.
     *
     * @param string|object $class
     * @param string $method
     * @return Comment
     */
    public function readMethod($class, $method)
    {
        return $this->process(new ReflectionMethod($class, $method));
    }

    /**
     * Reads and parses documentation comments from properties.
     *
     * @param string|object $class
     * @param string $property
     * @return Comment
     */
    public function readProperty($class, $property)
    {
        return $this->process(new ReflectionProperty($class, $property));
    }
}