<?php
namespace Yumaeda\Payment\Veritrans;

/**
 * Handles online credit card payment with veritrans.
 *
 * Example usage:
 *
 * <?php
 * use Yumaeda\Payment\Veritrans\Veritrans;
 * use Yumaeda\Payment\Veritrans\CreditCard;
 *
 * $order_id = '#0123456789';
 * $total = 99999;
 * $client_key = '<Client Key>';
 * $server_key = '<Server Key>';
 * $card_number = '<Card Number>';
 * $expire_month = '09';
 * $expire_year = '2022';
 * $cvv = '0000';
 *
 * $veritrans = new Veritrans($client_key, $server_key);
 * $credit_card = new CreditCard($card_number, $expire_month, $expire_year, $cvv);
 * $veritrans->setCreditCard($credit_card);
 * $veritrans->charge($order_id, $total, true);
 *
 * @package  veritrans
 * @author   Yukitaka Maeda <yumaeda@gmail.com>
 * @version  1.0.0
 * @access   public
 * @see      https://github.com/yumaeda
*/
class Veritrans
{
    /**
     * @var string Contains URI for tokens API
     */
    const TOKENS_API_URI = 'https://air.veritrans.co.jp/vtdirect/v1/tokens';

    /**
     * @var string Contains URI for charges API
     */
    const CHARGES_API_URI = 'https://air.veritrans.co.jp/vtdirect/v1/charges';

    /**
     * @var string $client_key Contains Client key for a veritrans API
     */
    private $client_key;

    /**
     * @var string $server_key Contains Server key for a veritrans API
     */
    private $server_key;

    /**
     * @var CreditCard $credit_card Contains credit card info
     */
    private $credit_card;

    /**
     * Constructor
     *
     * @access public
     * @param  string $client_key Client key for veritrans API
     * @param  string $server_key Server key for veritrans API
     * @return void
     */
    public function __construct($client_key, $server_key)
    {
        $this->client_key = $client_key;
        $this->server_key = $server_key;
    }

    /**
     * Sets credit card info
     *
     * @access public
     * @param CreditCard $credit_card Credit card info
     * @return void
     */
    public function setCreditCard($credit_card)
    {
        $this->credit_card = $credit_card;
    }

    /**
     * Returns result of the payment transaction
     *
     * @access public
     * @param  string $order_id Order ID for for the payment transaction
     * @param  int $total_amount Total amount (in JPY) for the payment transaction
     * @param  bool $with_capture Flag for making payment transaction with capture or not
     * @return mixed Payment transaction result
     */
    public function charge($order_id, $total_amount, $with_capture)
    {
        $response = $this->getTokenId();

        if (($response === false) || ($response->status !== 'success'))
        {
            return $response;
        }

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . base64_encode($this->server_key),
        ];
        $post_header = implode("\r\n", $headers);

        $post_data = [
            'order_id'     => $order_id,
            'token_id'     => $response->data->token_id,
            'gross_amount' => $total_amount,
            'with_capture' => $with_capture,
        ];

        return json_decode($this->post(self::CHARGES_API_URI, $post_header, json_encode($post_data)));
    }

    /**
     * Returns token ID, which is used for payment transaction
     *
     * @access private
     * @return mixed Token ID
     */
    private function getTokenId()
    {
        $query_string = http_build_query(
            [
                'card_number' => $this->credit_card->card_number,
                'card_exp_month' => $this->credit_card->exp_month,
                'card_exp_year' => $this->credit_card->exp_year,
                'card_cvv' => $this->credit_card->cvv,
                'client_key' => $this->client_key,
            ]
        );

        $param = [
            'http' => [
                'header' => 'Content-Type: application/json; charset=utf-8' . "\r\n",
                'method' => 'GET',
            ]
        ];

        $uri = self::TOKENS_API_URI . '?' . $query_string;
        $http_context = stream_context_create($param);
        $response = file_get_contents($uri, false, $http_context);

        return ($response !== false) ? json_decode($response) : false;
    }

    /**
     * Sends post request to the specified URI
     *
     * @access private
     * @param  string $uri URI to which POST request is being sent
     * @param  string $exp_month Expire month for the credit card
     * @param  string $exp_year Expire year for the credit card
     * @param  string $cvv CVV for the credit card
     * @throws \Exception
     * @return mixed Result of the POST request
     */
    private function post($uri, $data, $post_header = null)
    {
        $param = [
            'http' => [
                'method'  => 'POST',
                'content' => $data,
            ],
        ];

        if ($post_header !== null) {
            $param['http']['header'] = $post_header;
        }

        $http_context = stream_context_create($param);
        $fp  = @fopen($uri, 'rb', false, $http_context);

        if (!$fp) {
            throw new \Exception('Problem with ' . $uri . ', ' . error_get_last());
        }

        $response = @stream_get_contents($fp);
        fclose($fp);

        if ($response === false) {
            throw new \Exception('Problem reading data from ' . $uri . ', ' . error_get_last());
        }

        return $response;
    }
}

