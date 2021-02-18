<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\Exceptions\NetgsmRequiredFieldsException;
use TCGunel\Netgsm\SendSms\Params as SendSmsParams;
use TCGunel\Netgsm\CreditQuery\Params as CreditQueryParams;
use TCGunel\Netgsm\PackageCampaignQuery\Params as PackageCampaignQueryParams;

trait ParamsTrait
{
    /**
     * These fields are required by all services.
     *
     * @var string[]
     */
    protected $required = [];

    protected $map = [];

    /**
     * Sisteme giriş yaparken kullanılan kullanıcı adıdır. Bu alana abone numarası da yazılabilir (8xxxxxxxxx).
     * İstek yapılırken gönderilmesi zorunludur.
     *
     * @var string
     * @required
     */
    protected $username;

    /**
     * Sisteme giriş yaparken kullanılan şifredir. İstek yapılırken gönderilmesi zorunludur.
     *
     * @var string
     * @required
     */
    protected $password;

    /**
     * @param string[] $required
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     */
    protected function setRequired(array $required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @param array[] $map
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     */
    protected function setMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @param string $username
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $password
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $work_type
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     */
    protected function applyConfigParams(string $work_type)
    {
        $params = config("netgsm.{$work_type}.params");

        $common_params = [
            'username' => config('netgsm.username'),
            'password' => config('netgsm.password'),
        ];

        $params = array_merge($params ?? [], $common_params);

        foreach ($params as $key => $value) {

            if (in_array($this->$key, ['', null])) {

                $this->$key = $value;

            }

        }

        return $this;
    }

    /**
     * @return SendSmsParams|CreditQueryParams|PackageCampaignQueryParams|ParamsTrait
     * @throws \Exception
     */
    protected function validateParams()
    {
        $errors = [];

        foreach ($this->required as $key => $value) {

            if (in_array($this->$key, ['', null])) {

                $errors[$key] = $value;

            }

        }

        if (!empty($errors)) {

            $message = sprintf('Required fields for Netgsm are empty %s.', join(', ', array_keys($errors)));

            throw new NetgsmRequiredFieldsException($message, 422);

        }

        return $this;
    }

}
