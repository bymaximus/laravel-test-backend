<?php

namespace App\Containers\Geral\UI\WEB\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Support\Facades\Cache;
use App\Containers\Geral\Exceptions\InstallControllerAcaoInvalidaException;
use App\Containers\Geral\Data\Repositories\Cadastro\EstadoRepository;
use App\Containers\Geral\Models\Estado;
use App\Containers\Geral\Helpers\Helpers as Helpers;
use Carbon\Carbon;
use Exception;

class InstallController extends WebController
{
    private $preStarted = false;

    private $actionName = null;

    public function __construct()
    {
        $self = $this;
        $this->middleware(function ($request, $next) use ($self) {
            $self->temAcesso();
            return $next($request);
        });
    }

    public function temAcesso()
    {
        defined('ADMIN_USERNAME') || define('ADMIN_USERNAME', env('ADMIN_USERNAME', 'admin'));
        defined('ADMIN_PASSWORD') || define('ADMIN_PASSWORD', env('ADMIN_PASSWORD', 'admin'));
        if (!isset($_SERVER['PHP_AUTH_USER']) ||
            !isset($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||
            $_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD
        ) {
            Header('WWW-Authenticate: Basic realm="Login"');
            Header('HTTP/1.0 401 Unauthorized');

            echo '<html><body>';
            echo '<h1>Rejeitado!</h1>';
            echo '<big>Usuário ou senha inválidos!</big>';
            echo '</body></html>';
            die();
        }
        set_time_limit(0);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        config([
            'app.debug' => true,
        ]);
    }

    public function __invoke(Request $request, $action)
    {
        $this->temAcesso();
        if (!$action ||
            strtolower(substr($action, 0, 1)) == '_' ||
            !method_exists($this, $action)
        ) {
            throw new InstallControllerAcaoInvalidaException('Ação "' . $action . "' não localizada.");
        }

        $this->actionName = $action;
        $this->callAction($action, [$request]);
    }

    public function _printLine($message = '', $pre = true, $lineBreak = true, $flush = true)
    {
        if ($pre) {
            $this->_startLogPrint();
        }
        if ($message) {
            if (is_array($message)) {
                echo print_r($message, true);
            } else {
                echo $message;
            }
        }

        if ($pre) {
            $this->_endLogPrint();
        }

        if ($lineBreak) {
            echo "\n";
        }

        if ($flush) {
            @ob_flush();
            @flush();
        }
    }

    public function _startLogPrint($htmlOptions = ['style' => 'line-height:18px; font-size:12px; margin:0;'])
    {
        if ($htmlOptions &&
            is_array($htmlOptions)
        ) {
            echo '<pre ';
            foreach ($htmlOptions as $key => $value) {
                echo $key . '="' . $value . '" ';
            }
            echo '>';
        } else {
            echo '<pre>';
        }
        $this->preStarted = true;
    }

    public function _endLogPrint()
    {
        echo '</pre>';
        $this->preStarted = false;
    }

    public function _startLongTask(Request $request)
    {
        while (@ob_get_level()) {
            @ob_end_clean();
        }
        @ob_end_clean();
        set_time_limit(-1);
        @ini_set('output_compression', '0');
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');
        ob_implicit_flush(true);
        header('X-Accel-Buffering: no');
        header('Content-Encoding: none');

        $this->longTaskStarted = true; ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
            <?php
        if (!$this->actionName) {
            if ($request->route) {
                $actionName = $request->route->getActionMethod();
            } else {
                $actionName = 'Operations';
            }
        } else {
            $actionName = $this->actionName;
        }
        $this->_printLine('--=========== ' . (isset($this->titles[$actionName]) ? $this->titles[$actionName] : $actionName) . ' ===========--');
    }

    public function _endLongTask()
    {
        if ($this->preStarted) {
            $this->_endLogPrint();
        }
        $this->longTaskStarted = false;
        @ob_flush();
        @flush();
    }

    public function index(Request $request)
    {
        $this->_startLongTask($request);

        $reflection = new \ReflectionClass($this);
        $className = $reflection->getName();
        $methods = $reflection->getMethods();

        $now = Carbon::now();
        //$date->format('Y-m-d H:i:s')
        $this->_printLine($now);
        $this->_printLine('Avaliable actions:');

        foreach ($methods as $method) {
            if (preg_match('/^[^\_](\w{2,})/', $method->name, $matches) &&
                $method->class == $className
            ) {
                $action = $matches[0];
                if ($action != 'temAcesso') {
                    $actions = $action;
                    $this->_printLine('<a href="' . url('/admin/' . $actions) . '">/admin/' . $action . '</a>');
                }
            }
        }
        $this->_endLongTask();
    }

    public function phpVersion(Request $request)
    {
        phpinfo();
    }

    public function phpLoadedExtensions(Request $request)
    {
        $this->_startLongTask($request);
        $this->_printLine(get_loaded_extensions());
        $this->_endLongTask();
    }

    public function appConfig(Request $request)
    {
        $this->_startLongTask($request);
        $this->_printLine(config('app'));
        $this->_endLongTask();
    }

    public function environment(Request $request)
    {
        $this->_startLongTask($request);

        $command = 'env';
        $this->_printLine('Running command: ' . $command);
        $exitCode = Artisan::call($command, []);
        $this->_printLine('Exit Code: ' . $exitCode);
        $this->_printLine('Response: ' . Artisan::output());

        $this->_endLongTask();
    }

    public function filesViewer(Request $request)
    {
        $this->_startLongTask($request);
        //$this->_startLogPrint();

        $path = base_path() . '/' . request('path', '');
        if (!Helpers::fileSystem()::isDir($path)) {
            $this->_printLine('Invalid path "' . $path . '"');
        } else {
            $files = Helpers::fileSystem()::getAllFilesRecursively($path);
            if (!$files) {
                $this->_printLine('No files');
            } else {
                foreach ($files as $filePath) {
                    $this->_printLine($filePath . ' [' . date('Y-m-d h:i:s', filemtime($filePath)) . '] ');
                }
            }
        }

        //$this->_endLogPrint();
        $this->_endLongTask();
    }

    public function migrateUp(Request $request)
    {
        $migrate = request('migrate', '');
        if ($migrate != '1') {
            ?>
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
					<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
					<title>Migrate Up?</title>
				</head>
				<body>
					<form action="migrateUp" method="post">
						<input type="hidden" name="migrate" value="1" />
						<input type="hidden" name="_token" id="csrf-token" value="<?php echo csrf_token(); ?>" />
						<p>
							<label>Migrate Up?</label>
						</p>
						<p>
							<label>
								<input type="submit" name="Submit" value="Sim" />
							</label>
						</p>
					</form>
				</body>
			</html>
			<?php
            die;
        } else {
            $this->_startLongTask($request);
            $command = 'migrate';
            $this->_printLine('Running command: ' . $command);
            $exitCode = Artisan::call($command, []);
            $this->_printLine('Exit Code: ' . $exitCode);
            $this->_printLine('Response: ' . Artisan::output());
            $this->_endLongTask();
        }
    }

    public function execute(Request $request)
    {
        $query = request('query', ''); ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        	<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
				<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
        		<title>Exec</title>
        	</head>
        	<body>
				<form action="execute" method="post">
					<input type="hidden" name="_token" id="csrf-token" value="<?php echo csrf_token(); ?>" />
					<p>
						<label>Exec command
						<textarea name="query" cols="100" rows="20"><?php echo $query; ?></textarea>
						</label>
					</p>
					<p>
						<label>
							<input type="submit" name="Submit" value="Submit" />
						</label>
					</p>
				</form>
				<?php
                if ($query) {
                    exec($query, $result);
                    if (isset($result)) {
                        echo 'Result:';
                        echo '<pre>';
                        echo implode("\n", $result);
                        echo '</pre>';
                    }
                } ?>
        	</body>
		</html>
		<?php
        die;
    }

    public function laravelLog(Request $request)
    {
        $this->_startLongTask($request);
        $file = base_path() . '/storage/logs/laravel.log';
        if (!Helpers::fileSystem()::fileExists($file)) {
            $file = base_path() . '/storage/logs/laravel-' . date('Y-m-d') . '.log';
            if (!Helpers::fileSystem()::fileExists($file)) {
                $this->_printLine('Not found "' . $file . '"');
            } else {
                $contents = file_get_contents($file);
                $this->_printLine($contents);
            }
        } else {
            $contents = file_get_contents($file);
            $this->_printLine($contents);
        }
        $this->_endLongTask();
    }

    public function clearLaravelLog(Request $request)
    {
        $this->_startLongTask($request);
        $file = base_path() . '/storage/logs/laravel.log';
        if (!Helpers::fileSystem()::fileExists($file)) {
            $file = base_path() . '/storage/logs/laravel-' . date('Y-m-d') . '.log';
            if (!Helpers::fileSystem()::fileExists($file)) {
                $this->_printLine('Not found "' . $file . '"');
            } else {
                file_put_contents($file, "LOG CLEARED\r\n");
                $this->_printLine('LOG CLEARED');
            }
        } else {
            file_put_contents($file, "LOG CLEARED\r\n");
            $this->_printLine('LOG CLEARED');
        }
        $this->_endLongTask();
    }

    public function cleanOpCache()
    {
        if (!function_exists('opcache_reset')) {
            echo 'opcache not installed';
        } elseif (opcache_reset()) {
            echo 'OK';
        } else {
            echo 'FAIL';
        }
    }

    public function memcacheTest(Request $request)
    {
        $this->_startLongTask($request);
        $this->_startLogPrint();
        Cache::store('memcached')->put('foo', 'bar', 60);
        var_dump(Cache::store('memcached')->get('foo'));
        $this->_endLogPrint();
        $this->_endLongTask();
    }

    public function databaseAccessTest(Request $request)
    {
        $this->_startLongTask($request);
        $this->_startLogPrint();

        try {
            Estado::flushQueryCache();
            $repo = new EstadoRepository(app());
            $model = $repo->first();
            var_dump($model->nome);
        } catch (Exception $err) {
            var_dump($err->getMessage());
        }

        $this->_endLogPrint();
        $this->_endLongTask();
    }
}
