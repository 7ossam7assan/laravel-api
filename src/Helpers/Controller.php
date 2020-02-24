<?php

namespace Barista\Helpers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Barista\Contracts\Writable;

final class controller implements Writable
{
    const STUB_PATH = __DIR__.'/../../stubs/controller/';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        // init stubs
        $this->controllerClass = $this->filesystem->get(self::STUB_PATH.'class.stub');
    }

    public function populateNamespace()
    {
        $this->controllerClass = str_replace('DummyNamespace', 'App\Http\Controllers', $this->controllerClass);
    }

    public function populateName($controller)
    {
        $this->controllerName = $controller;
        $this->controllerClass = str_replace('DummyClass', $this->controllerName, $this->controllerClass);
    }

    public function populateMethods($methods)
    {
        $controllersMethods = null;

        $this->controllerClass = str_replace('// imports',  "use Illuminate\Http\Request;", $this->controllerClass);

        foreach($methods as $method) {
            $methodStub = $this->filesystem->get(self::STUB_PATH.'method.stub');

            $controllersMethods .= PHP_EOL . str_replace("DummyMethod", $method, $methodStub). PHP_EOL;
        }
    
        $this->controllerClass = str_replace('// methods',  trim($controllersMethods), $this->controllerClass);
    }

    public function write()
    {
        $this->filesystem->put(app_path().'/Http/Controllers/'.$this->controllerName .'.php', $this->controllerClass);
    }
}
