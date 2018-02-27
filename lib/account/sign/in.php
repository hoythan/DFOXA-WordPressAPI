<?php

namespace account\sign;

class in extends sign
{
    public function run()
    {
        $query = bizContentFilter(array(
            'type',
            'field',
            'value'
        ));

        $request = sign::signInAccount(array(
            'type' => $query->type,
            'field' => $query->field,
            'value' => $query->value
        ));

        dfoxaGateway($request);
    }
}