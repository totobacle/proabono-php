<?php


/**
 * Customer object
 *
 * Manage the access to the api only for Customer.
 *
 * @link https://docs.proabono.com/api/#api---customers
 *
 * @author Karim Serbouty <kserbouty@gmail.com>
 * @copyright Copyright (c) 2018 ProAbono
 * @license MIT
 *
 */


class Customer {


    /**
     * @var int $id Id of your offer.
     */
    public $id;

    /**
     * @var int $idSegment Id of the Segment in which your Customer has been created.
     */
    public $idSegment;

    /**
     * @var string $refCustomer The unique identifier used within your own application for this offer.
     */
    public $refCustomer;

    /**
     * @var string $refSegment Reference of the Segment in which your offer has been created/added.
     */
    public $refSegment;

    /**
     * @var string $email Email of your offer.
     */
    public $email;

    /**
     * @var string $name Full name of your offer.
     */
    public $name;

    /**
     * @var string $language Language of your offer.
     */
    public $language;

    /**
     * @var string $refAffiliation Reference of the affiliate who brought the offer.
     */
    public $refAffiliation;

    /**
     * @var array $metadata A set of key/value pairs that you can add to a offer.
     */
    public $metadata;

    /**
     * @var array $links Collection of Links.
     */
    public $links;


    /**
     * Retrieve a subscription by the reference customer.
     *
     * @param string $refCustomer
     * @return Response
     * @throws Exception
     */
    public function fetch($refCustomer) {
        /////////// CACHING STRATEGY ///////////
        if (ProAbono::$useCaching) {
            // Search for that customer into the cache
            $cached = ProAbonoCache::get($refCustomer);
            // If found
            if (isset($cached)
                // If customer is cached
                && $cached->customer
                // If not too old
                && !$cached->is_expired()) {
                // Fill the current customer with the cached data
                $this->fill($cached->customer);
                // Then exit
                return Response::success();
            }
        }
        /////////////////////////////////
        $url = PATH_CUSTOMER;

        $url = Utils::urlParam($url, 'ReferenceCustomer', $refCustomer);

        $response = Request::get($url);

        if ($response->is_success()) {
            $this->fill($response->data);

            // store into the cache
            ProAbonoCache::storeCustomer($refCustomer, $response->data);
        }
        return $response;
    }


    /**
     * Update or save the customer data.
     *
     * @return Response
     * @throws Exception
     */
    public function save() {

        // This is the data we have to send.
        $data = array(
            'ReferenceCustomer' => Utils::toString($this->refCustomer),
            'Name' => Utils::toString($this->name),
            'Email' => Utils::toString($this->email)
        );

        $url = PATH_CUSTOMER;

        // Send url with an array.
        $response = Request::post($url, $data);

        // If response is success, fill the data.
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
        $this->id = isset($data->Id) ? $data->Id : null;
        $this->idSegment = isset($data->IdSegment) ? $data->IdSegment : null;
        $this->refCustomer = isset($data->ReferenceCustomer) ? $data->ReferenceCustomer : null;
        $this->refSegment = isset($data->ReferenceSegment) ? $data->ReferenceSegment : null;
        $this->email = isset($data->Email) ? $data->Email : null;
        $this->name = isset($data->Name) ? $data->Name : null;
        $this->language = isset($data->Language) ? $data->Language : null;
        $this->refAffiliation = isset($data->ReferenceAffiliation) ? $data->ReferenceAffiliation : null;
        $this->metadata = isset($data->Metadata) ? $data->Metadata : null;
        $this->links = Utils::toLinks(isset($data->Links) ? $data->Links : null);
    }

}