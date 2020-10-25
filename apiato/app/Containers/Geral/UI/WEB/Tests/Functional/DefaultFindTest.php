<?php

namespace App\Containers\Geral\UI\WEB\Tests\Functional;

use App\Containers\Geral\Tests\WebTestCase;

/**
 * Class DefaultFindTest.
 *
 * @group frontend
 */
class DefaultFindTest extends WebTestCase
{
    protected $endpoint = 'get@/cadastro/estado/{id}';

    protected $access = [
        'roles'       => '',
        'permissions' => '',
    ];

    /**
     * @test
     */
    public function testObterEstado_()
    {
        $data = [
            'id' => 12
        ];

        $response = $this->injectId($data['id'], true)->makeCall();

        $response->assertStatus(200);

        $responseContent = $this->getResponseContentObject();

        $this->assertNotEmpty($responseContent);
        $this->assertObjectHasAttribute('registro', $responseContent);

        $this->assertNotEmpty($responseContent->registro);

        $this->assertObjectHasAttribute('nome', $responseContent->registro);
        $this->assertObjectHasAttribute('sigla', $responseContent->registro);

        $this->assertNotEmpty($responseContent->registro->nome);
        $this->assertNotEmpty($responseContent->registro->sigla);
    }
}
