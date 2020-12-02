<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blockchain {
    private $url = 'http://121.133.140.199:4003';
    private $address = '5C5W6PAsJJr8891RrekVbYAVjpZd7HSVcrZ4b7xvKkqJuGxB';
    private $password = '123';

	/**
	 * This is a static class, do not instantiate it
	 *
	 * @codeCoverageIgnore
	 */
	public function __construct() {}

    public function mint($tokenId, $tokenString) {
        $method = '/user/mint';
        $data = [
            'password' => $this->password,
            'token_id' => $tokenId,
            'token_string' => json_encode($tokenString)
        ];

        return $this->json_rpc($method, $data);
    }

    public function call($tokenId) {
        $method = '/user/call';
        $data = [
            'callname' => 'read_string',
            'token_id' => $tokenId,
            'address' => $this->address
        ];

        return $this->json_rpc($method, $data);
    }

	public function query($extrincs) {
        $method = '/user/query';
        $data = [
            'extrincs' => $extrincs
        ];

        return $this->json_rpc($method, $data);
    }

    function json_rpc($method, $data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }
}
