<?php

class SRApi
{
    const API_BASE_URL = 'https://de.backend.salesrender.com/companies/';
    const API_USER_SCOPE = '/CRM/user';
    const API_CRM_SCOPE = '/CRM';
    const API_CPA_SCOPE = '/CPA';
    private $token;
    private $companyId;
    private $scope;

    public function __construct($companyId, $token, $scope = self::API_CRM_SCOPE)
    {
        $this->companyId = $companyId;
        $this->token = $token;

        $this->scope = $scope;
    }

    private function getsJsonRecursive($data)
    {
        if (is_array($data)) {
            $json = '{';
            $isFirst = true;
            foreach ($data as $key => $value) {
                if (!$isFirst) {
                    $json .= ',';
                }
                $json .= (!is_int($key)) ? '"' . addslashes($key) . '":' . $this->getsJsonRecursive($value) : $this->getsJsonRecursive($value);
                $isFirst = false;
            }
            $json .= '}';
            return $json;
        } elseif (is_string($data)) {
            return '"' . addslashes($data) . '"';
        } else {
            return $data;
        }
    }

    public function jsonEncode($array)
    {
        $json = $this->getsJsonRecursive($array);
        $json = str_replace('{{', '[{', $json);
        $arrayJson = str_split($json);
        $newJsonArray = [];
        $acc = [];

        foreach ($arrayJson as $char) {
            if ($char === '{' || $char === '[') {
                $acc[] = $char;
            } elseif ($char === '}' && $acc[count($acc) - 1] === '{') {
                array_pop($acc);
            } elseif ($char === '}' && $acc[count($acc) - 1] === '[') {
                $char = ']';
                array_pop($acc);
            }
            $newJsonArray[] = $char;
        }
        $json = implode('', $newJsonArray);
        return $json;
    }

    public function sendRequest($query, array $vars = null)
    {
        if (empty($query)) {
            throw new \Exception('Query must not be empty');
        }

        $postFields = [
            'query' => $query
        ];

        if (!empty($vars)) {
            $postFields['variables'] = $vars;
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->getApiUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->jsonEncode($postFields),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function sendCustomRequest($url, array $postFields = [], array $getFields = null)
    {
        $ch = curl_init();
        $getLine = '';
        if (!empty($getFields)) {
            $getLine = "?" . http_build_query($getFields);
        }
        curl_setopt_array($ch, [
            CURLOPT_URL => $url . $getLine,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => (!empty($postFields)) ? true : false,
            CURLOPT_POSTFIELDS => (!empty($postFields)) ? http_build_query($postFields) : null
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function getApiUrl()
    {
        return self::API_BASE_URL . $this->companyId . $this->scope . '?token=' . $this->token;
    }
}
