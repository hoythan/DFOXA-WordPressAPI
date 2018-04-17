<?php

namespace account\sign;

class up extends sign
{
    public function run()
    {
        $query = bizContentFilter(array(
            'type',
            'field',
            'value',
            'create_user'
        ));

        $request = sign::signUpAccount(array(
            'type' => $query->type,
            'field' => $query->field,
            'value' => $query->value
        ),objectToArray($query->create_user));

        dfoxaGateway($request);

    }
}