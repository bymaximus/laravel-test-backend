<?php

namespace App\Containers\Geral\Tests;

use App\Containers\Geral\Tests\TestCase as BaseTestCase;

/**
 * Class WebTestCase
 *
 * Container Web TestCase class. Use this class to put your Web container specific tests helper functions.
 *
 */
class WebTestCase extends BaseTestCase
{
    protected $subDomain = 'backend';

    public function overrideSubDomain($url = null)
    {
        $url = env('APP_URL', null);

        $info = parse_url($url);

        $array = explode('.', $info['host']);

        $withoutDomain = (array_key_exists(
            count($array) - 2,
            $array
        ) ? $array[count($array) - 2] : '') . '.' . $array[count($array) - 1];

        $newSubDomain = $info['scheme'] . '://' . $this->subDomain . '.' . $withoutDomain;

        return $this->baseUrl = $newSubDomain;
    }

    public function getTestingUser($userDetails = null, $access = null)
    {
    }

    public function setUp(): void
    {
        $url = env('APP_URL', null);

        $info = parse_url($url);

        $array = explode('.', $info['host']);

        $withoutDomain = (array_key_exists(
            count($array) - 2,
            $array
        ) ? $array[count($array) - 2] : '') . '.' . $array[count($array) - 1];

        $newSubDomain = $info['scheme'] . '://' . $this->subDomain . '.' . $withoutDomain;

        putenv("API_URL=".$newSubDomain);
        parent::setUp();
    }

    public function tearDown(): void
    {
        putenv("API_URL=");
        parent::tearDown();
    }
}
