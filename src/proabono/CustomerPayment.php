<?php

/**
 * Customer Payment Settings object
 *
 * Manage the access to the api for the payment settings.
 *
 * @link https://docs.proabono.com/api/#api---customers
 *
 * @author Karim Serbouty <kserbouty@gmail.com>
 * @copyright Copyright (c) 2018 ProAbono
 * @license MIT
 *
 */

class CustomerPayment {

    /**
     * @var
     */
    public $typePayment;

    /**
     * @var
     */
    public $dateNextBilling;

    /**
     * Retrieve a payment settings by the reference customer.
     *
     * @param string $refCustomer
     * @return Response
     * @throws Exception
     */
    public function fetch($refCustomer) {

        $url = PATH_PAYMENT_SETTINGS;

        $url = Utils::urlParam($url, 'ReferenceCustomer', $refCustomer);

        $response = Request::get($url);

        if ($response->is_success()) {
            $this->fill($response->data);
        }
        return $response;
    }

    /**
     * Save the payment settings by the reference customer.
     *
     * @param $refCustomer
     * @return Response
     * @throws Exception
     */
    public function save($refCustomer) {

        // This is the data we send.
        $data = array(
          "TypePayment" => Utils::toString($this->typePayment),
          "DateNextBilling" => Utils::toString($this->dateNextBilling)
        );

        $url = PATH_PAYMENT_SETTINGS;

        $url = Utils::urlParam($url, 'ReferenceCustomer', $refCustomer);

        // Send url with an array.
        $response = Request::post($url, $data);

        if ($response->is_success()) {
            $this->fill($response->data);
        }
        return $response;

    }

    /**
     * Fill our object with the raw ProAbono data.
     *
     * @param $data
     */
    public function fill($data) {
        $this->typePayment = isset($data->TypePayment) ? $data->TypePayment : null;
        $this->dateNextBilling = isset($data->DateNextBilling) ? $data->DateNextBilling : null;
    }

}
