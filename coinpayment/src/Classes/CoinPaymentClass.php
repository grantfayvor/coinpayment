<?php
namespace Harrison\CoinPayment\Classes;

class CoinPaymentClass
{

//    use coinPaymentTrait;

    private $private_key;
    private $public_key;

    public function setup($public_key, $private_key)
    {
        $this->public_key = $public_key;
        $this->private_key = $private_key;
    }

    public function url_payload($payload = [])
    {
        $data['note'] = empty($payload['note']) ? '' : $payload['note'];
        $data['amountTotal'] = empty($payload['amountTotal']) ? 1 : $payload['amountTotal'];
        $data['items'] = empty($payload['items']) ? [] : $payload['items'];
        $data['csrf'] = session()->token();
        $data['params'] = empty($payload['params']) ? [] : $payload['params'];
        $data['payload'] = empty($payload['payload']) ? [] : $payload['payload'];

        $params = base64_encode(serialize($data));
        return route('coinpayment.create.transaction', $params);
    }

    public function withdrawal_url_payload($payload = [])
    {
        $data['note'] = empty($payload['note']) ? '' : $payload['note'];
        $data['amountTotal'] = empty($payload['amountTotal']) ? 1 : $payload['amountTotal'];
        $data['items'] = empty($payload['items']) ? [] : $payload['items'];
        $data['csrf'] = session()->token();
        $data['params'] = empty($payload['params']) ? [] : $payload['params'];
        $data['payload'] = empty($payload['payload']) ? [] : $payload['payload'];
        $data['address'] = empty($payload['address']) ? '' : $payload['address'];

        $params = base64_encode(serialize($data));
        return route('coinpayment.createwithdrawal', $params);
    }

    public function get_payload($serialize)
    {
        $data = unserialize(base64_decode($serialize));
        if (empty($data['csrf']) || $data['csrf'] !== session()->token())
            return abort(404);
        unset($data['csrf']);

        return $data;
    }

    public function link_transaction_list()
    {
        return route('coinpayment.transaction.list');
    }

    public function api_call($cmd, $req = array())
    {
        // Fill these in from your API Keys page
        $this->public_key = $this->public_key ?: config('coinpayment.public_key');
        $this->private_key = $this->private_key ?: config('coinpayment.private_key');

        // Set the API command and required fields
        $req['version'] = 1;
        $req['cmd'] = $cmd;
        $req['key'] = $this->public_key;
        $req['format'] = 'json'; //supported values are json and xml

        // Generate the query string
        $post_data = http_build_query($req, '', '&');

        // Calculate the HMAC signature on the POST data
        $hmac = hash_hmac('sha512', $post_data, $this->private_key);

        // Create cURL handle and initialize (if needed)
        static $ch = NULL;
        if ($ch === NULL) {
            $ch = curl_init('https://www.coinpayments.net/api.php');
            curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('HMAC: ' . $hmac));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // Execute the call and close cURL handle
        $data = curl_exec($ch);
        // Parse and return data if successful.
        if ($data !== FALSE) {
            if (PHP_INT_SIZE < 8 && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                // We are on 32-bit PHP, so use the bigint as string option. If you are using any API calls with Satoshis it is highly NOT recommended to use 32-bit PHP
                $dec = json_decode($data, TRUE, 512, JSON_BIGINT_AS_STRING);
            } else {
                $dec = json_decode($data, TRUE);
            }
            if ($dec !== NULL && count($dec)) {
                return $dec;
            } else {
                // If you are using PHP 5.5.0 or higher you can use json_last_error_msg() for a better error message
                return array('error' => 'Unable to parse JSON result (' . json_last_error() . ')');
            }
        } else {
            return array('error' => 'cURL error: ' . curl_error($ch));
        }
    }
}
