<?php declare(strict_types=1);

namespace Yay;

class CompilerPass extends MacroMember {
    private
        $callback
    ;

    function __construct(array $ast) {
        if ($ast) $this->callback = $this->compileAnonymousFunction($ast);
    }

    function apply(Ast $ast, TokenStream $ts, Index $startNode, Index $endNode, Engine $engine) {
        if ($this->callback) ($this->callback)($ast, $ts, $startNode, $endNode, $engine);
    }

    private function compileAnonymousFunction(array $arg) : \Closure {
        $arglist = implode('', $arg['args']);
        $body = implode('', $arg['body']);
        $source = <<<PHP
<?php return static function({$arglist}) {
    {$body}
};
PHP;
        $file = sys_get_temp_dir() . '/yay-function-' . sha1($source);

        if (! is_readable($file)) file_put_contents($file, $source);

        return include $file;
    }
}
