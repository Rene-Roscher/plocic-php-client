<?php
/**
 * Created by PhpStorm.
 * User: Rene_Roscher
 * Date: 20.10.2019
 * Time: 02:22
 */

namespace Plocic\Manager;


use Plocic\Plocic;

class PaymentManager
{

    private $plocic;

    public function __construct(Plocic $plocic)
    {
        $this->plocic = $plocic;
    }

	/**
     * @param string $method
     * @param int $amount
     * @param $description | optional
     * @param $ok_url
     * @param $nok_url
     * @param $notify_url | optional
	 *
	 * @return payment_url | generated url from payment provider
     */
    public function create($method, $amount, $description = null, $ok_url, $nok_url, $notify_url = null)
    {
        return $this->plocic->get([
            'type' => $method,
            'amount' => $amount,
            'description' => $description,
            'success_url' => $ok_url,
            'failure_url' => $nok_url,
            'notification_url' => $notify_url
        ], 'payment/create')->data->payment->payment_url;
    }
	
	/**
	 * @param integer $transaction_id
	 */
    public function check(int $transaction_id) : bool
    {
        return boolval($this->plocic->get([
            'transaction_id' => $transaction_id
        ], 'Transactions')->data->state == 'success');
    }
    
}