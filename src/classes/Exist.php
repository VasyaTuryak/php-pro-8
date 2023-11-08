<?php

class Exist
{

    public function exist(string $url): mixed
    {

            $header = get_headers($url);


       return $header[0];
    }
}