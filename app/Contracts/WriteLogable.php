<?php

namespace App\Contracts;

interface WriteLogable
{
    /**
     * Action on model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function onModel();

    /**
     * handle on organization. Return by ID string of organization
     *
     * @return string
     */
    public function organId();

    /**
     * The person who executed this action
     *
     * @return \App\Models\User
     */
    public function executor();

    /**
     * CREATE_LOG_TYPE, EDIT_LOG_TYPE or DELETE_LOG_TYPE.
     * See constant
     *
     * @return int
     */
    public function logType();
}
