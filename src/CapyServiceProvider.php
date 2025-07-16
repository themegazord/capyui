<?php

namespace Capy;

use Capy\View\Components\Input;
use Capy\View\Components\Internal\Input as InternalInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CapyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/capy.php', 'capy');
    }

    public function boot()
    {
        $this->registerComponents();
        $this->registerBladeDirectives();
        $this->publishes([
            __DIR__ . '/../resources/themes' => public_path('vendor/capy/themes'),
        ], 'capy-themes');
        $this->publishes([
            __DIR__.'/../config/capy.php' => config_path('capy.php'),
        ], 'capy-config');
    }

    public function registerComponents(): void
    {
        $prefix = config('capy.prefix');
        Blade::component($prefix . 'input', Input::class);
        Blade::component('capy-input', InternalInput::class);
    }

    public function registerBladeDirectives(): void
    {
        $this->registerScopeDirective();
    }

    /**
     * Custom Blade directives: @scope and @endscope.
     *
     * This code snippet was adapted from the repository:
     * https://github.com/robsontenorio/mary
     *
     * All credits go to Robson Tenório (@robsontenorio).
     * It was merely copied and modified to fit a very specific use case in this project.
     */
    public function registerScopeDirective(): void
    {
        /**
         * All credits from this blade directive goes to Konrad Kalemba.
         * Just copied and modified for my very specific use case.
         *
         * https://github.com/konradkalemba/blade-components-scoped-slots
         */
        Blade::directive('scope', function ($expression) {
            // Split the expression by `top-level` commas (not in parentheses)
            $directiveArguments = preg_split("/,(?![^\(\(]*[\)\)])/", $expression);
            $directiveArguments = array_map('trim', $directiveArguments);

            [$name, $functionArguments] = $directiveArguments;

            // Build function "uses" to inject extra external variables
            $uses = Arr::except(array_flip($directiveArguments), [$name, $functionArguments]);
            $uses = array_flip($uses);
            array_push($uses, '$__env');
            array_push($uses, '$__bladeCompiler');
            $uses = implode(',', $uses);

            /**
             *  Slot names can`t contains dot , eg: `user.city`.
             *  So we convert `user.city` to `user___city`
             *
             *  Later, on component it will be replaced back.
             */
            $name = str_replace('.', '___', $name);

            return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$loop = null; \$__env->slot({$name}, function({$functionArguments}) use ({$uses}) { \$loop = (object) \$__env->getLoopStack()[0] ?>";
        });

        Blade::directive('endscope', function () {
            return '<?php }); ?>';
        });
    }
}
