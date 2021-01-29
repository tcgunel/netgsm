<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\SendSms\Params;

trait ParamsTrait
{
    /**
     * These fields are required by all services.
     *
     * @var string[]
     */
    public $required = [];

    public $map = [];

    /**
     * Sisteme giriş yaparken kullanılan kullanıcı adıdır. Bu alana abone numarası da yazılabilir (8xxxxxxxxx).
     * İstek yapılırken gönderilmesi zorunludur.
     *
     * @var string
     * @required
     */
    public $username;

    /**
     * Sisteme giriş yaparken kullanılan şifredir. İstek yapılırken gönderilmesi zorunludur.
     *
     * @var string
     * @required
     */
    public $password;

    /**
     * @param string[] $required
     * @return Params|ParamsTrait
     */
    public function setRequired(array $required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @param array[] $map
     * @return Params|ParamsTrait
     */
    public function setMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @param string $username
     * @return Params|ParamsTrait
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @param string $password
     * @return Params|ParamsTrait
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $work_type
     * @return Params|ParamsTrait
     */
    public function applyConfigParams(string $work_type)
    {
        $params = config("netgsm.{$work_type}.params");

        $common_params = [
            'username' => config('netgsm.username'),
            'password' => config('netgsm.password'),
        ];

        $params = array_merge($params, $common_params);

        foreach ($params as $key => $value) {

            if (in_array($this->$key, ['', null])) {

                $this->$key = $value;

            }

        }

        return $this;
    }

    /**
     * @return Params|ParamsTrait
     * @throws \Exception
     */
    public function validateParams()
    {
        $errors = [];

        foreach ($this->required as $key => $value) {

            if (in_array($this->$key, ['', null])) {

                $errors[$key] = $value;

            }

        }

        if (!empty($errors)) {

            $message = sprintf('Required fields are empty %s.', join(', ', array_keys($errors)));

            throw new \Exception($message, 422);

        }

        return $this;
    }

}
