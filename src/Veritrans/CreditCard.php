<?php
namespace Yumaeda\Payment\Veritrans;

/**
 * Represents a credit card
 *
 * @package  veritrans
 * @author   Yukitaka Maeda <yumaeda@gmail.com>
 * @version  1.0.0
 * @access   public
 * @see      https://github.com/yumaeda
*/
class CreditCard
{
    /**
     * @var string $card_number Contains credit card number
     */
    public $card_number;

    /**
     * @var string $exp_month Contains expiration month for credit card
     */
    public $exp_month;

    /**
     * @var string $exp_year Contains expiration year for credit card
     */
    public $exp_year;

    /**
     * @var string $cvv Contains CVV for credit card
     */
    public $cvv;

    /**
     * Constructor
     *
     * @access public
     * @param string $card_number Credit card number
     * @param int $exp_month Expiration month for credit card
     * @param int $exp_year Expiration year for credit card
     * @var string $cvv CVV for credit card
     * @return void
     */
    public function __construct($card_number, $exp_month, $exp_year, $cvv)
    {
        $this->card_number = $card_number;
        $this->exp_month = substr('0' . $exp_month, -2);
        $this->exp_year = (string) $exp_year;
        $this->cvv = $cvv;
    }
}

