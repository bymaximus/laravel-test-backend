<?php

namespace App\Containers\Geral\Tests\Unit;

use App\Containers\Geral\Tests\TestCase;
use App\Containers\Geral\Actions\Cadastro\Estado\AdicionarAction;
use App\Containers\Geral\Actions\Cadastro\Estado\AtualizarRegistroAction;
use App\Containers\Geral\Actions\Cadastro\Estado\ObterRegistroAction;
use App\Containers\Geral\Actions\Cadastro\Estado\RemoverAction;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\ObterRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\AdicionarRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\AtualizarRegistroRequest;
use App\Containers\Geral\UI\WEB\Requests\Cadastro\Estado\RemoverRequest;
use App\Containers\Geral\Models\Estado;
use Illuminate\Support\Facades\App;

/**
 * Class DefaultUnitTest.
 *
 * @group backend
 */
class DefaultUnitTest extends TestCase
{

    /**
     * @test
     */
    public function testCriarEstado_()
    {
        $testModels = [];

        $data = [
            'nome' => 'UNITTEST',
            'sigla' => 'UT'
        ];

        $request = new AdicionarRequest($data);

        $action = App::make(AdicionarAction::class);

        Estado::flushQueryCache();
        $model = $action->run($request);

        $this->assertNotEmpty($model);
        $this->assertInstanceOf(Estado::class, $model);
        $this->assertEquals($model->nome, $data['nome']);
        $this->assertEquals($model->sigla, $data['sigla']);

        $testModels['estado'] = $model;

        return $testModels;
    }

    /**
     * @test
     * @depends testCriarEstado_
     */
    public function testObterEstado_(array $testModels)
    {
        $this->assertNotEmpty($testModels);
        $this->assertArrayHasKey('estado', $testModels);
        $this->assertInstanceOf(Estado::class, $testModels['estado']);
        $this->assertNotEmpty($testModels['estado']->id);

        $data = [
            'id' => 12
        ];
        $request = new ObterRegistroRequest($data);
        $request->id = $data['id'];

        $action = App::make(ObterRegistroAction::class);

        Estado::flushQueryCache();
        $model = $action->run($request);

        $this->assertNotEmpty($model);
        $this->assertInstanceOf(Estado::class, $model);
        $this->assertEquals($model->id, $data['id']);

        $testModels['estado'] = $model;

        return $testModels;
    }

    /**
     * @test
     * @depends testObterEstado_
     */
    public function testAtualizarEstado_(array $testModels)
    {
        $this->assertNotEmpty($testModels);
        $this->assertArrayHasKey('estado', $testModels);
        $this->assertInstanceOf(Estado::class, $testModels['estado']);
        $this->assertNotEmpty($testModels['estado']->id);

        $data = [
            'id' => $testModels['estado']->id,
            'nome' => 'UNITTEST2',
            'sigla' => 'TU'
        ];

        $request = new AtualizarRegistroRequest($data);
        $request->id = $data['id'];
        $request->nome = $data['nome'];
        $request->sigla = $data['sigla'];

        $action = App::make(AtualizarRegistroAction::class);

        Estado::flushQueryCache();
        $model = $action->run($request);

        $this->assertNotEmpty($model);
        $this->assertInstanceOf(Estado::class, $model);
        $this->assertEquals($model->id, $data['id']);
        $this->assertEquals($model->nome, $data['nome']);
        $this->assertEquals($model->sigla, $data['sigla']);

        $testModels['estado'] = $model;

        return $testModels;
    }

    /**
     * @test
     * @depends testAtualizarEstado_
     */
    public function testRemoverEstado_(array $testModels)
    {
        $this->assertNotEmpty($testModels);
        $this->assertArrayHasKey('estado', $testModels);
        $this->assertInstanceOf(Estado::class, $testModels['estado']);
        $this->assertNotEmpty($testModels['estado']->id);

        $data = [
            'id' => $testModels['estado']->id
        ];
        $request = new RemoverRequest($data);
        $request->id = $data['id'];

        $action = App::make(RemoverAction::class);

        Estado::flushQueryCache();
        $model = $action->run($request);

        $this->assertNotEmpty($model);
        $this->assertInstanceOf(Estado::class, $model);
        $this->assertEquals($model->id, $data['id']);
        $this->assertEquals($model->trashed(), true);
    }
}
