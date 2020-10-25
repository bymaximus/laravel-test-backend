<?php

namespace App\Containers\Geral\Providers;

use Illuminate\Auth\EloquentUserProvider;

class CustomEloquentUserProvider extends EloquentUserProvider
{
    /**
    * Retrieve a user by their unique identifier.
    *
    * @param  mixed  $identifier
    * @return \Illuminate\Contracts\Auth\Authenticatable|null
    */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        $key = $model->getAuthIdentifierName();
        if ($key &&
            is_array($key)
        ) {
            $key = $key[0];
        }

        return $this->newModelQuery($model)
                    ->where($key, $identifier)
                    ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(),
            $identifier
        )->first();

        if (!$retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
                        ? $retrievedModel : null;
    }
}
