<?php

namespace TCGunel\Netgsm\Interfaces;

use TCGunel\Netgsm\SendSms\Params;
use TCGunel\Netgsm\Traits\ParamsTrait;

interface ParamsInterface{

    /**
     * @param string[] $required
     * @return Params|ParamsTrait
     */
    public function setRequired(array $required);

    /**
     * @param array[] $map
     * @return Params|ParamsTrait
     */
    public function setMap(array $map);

    /**
     * @param string $username
     * @return Params|ParamsTrait
     */
    public function setUsername(string $username);

    /**
     * @param string $password
     * @return Params|ParamsTrait
     */
    public function setPassword(string $password);

    /**
     * @param string $work_type
     * @return Params|ParamsTrait
     */
    public function applyConfigParams(string $work_type);

    /**
     * @return Params|ParamsTrait
     * @throws \Exception
     */
    public function validateParams();

    /**
     * @param string $service_type
     * @return Params
     * @throws \Exception
     */
    public function formatParamsByService(string $service_type): Params;
}
