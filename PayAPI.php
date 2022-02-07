<?php

    class PayAPI {
        private $SECRET_KEY;

        /**
         * @param string $SECRET_KEY
         * 
         * <p>Your paystack SECRET_KEY</p>
         */
        function __construct($SECRET_KEY){
            $this->SECRET_KEY = $SECRET_KEY;
        }

        /**
         * @param int $amount
         * 
         * <p>Amount to be sent</p>
         * 
         * @param string $email
         * 
         * <p>Email of customer</p>
         * 
         * @param string $callbackUrl
         * 
         * <p>Redirect url</p>
         * 
         * @param string $referenceId
         * 
         * <p>Unique ID for your transactions </p>
         * optional
         * 
         * @return array TransactionParameter
         */
        function initializeTransaction (int $amount, string $email, string $callbackUrl, string $referenceId = "") {
            $url = "https://api.paystack.co/transaction/initialize";

            $fields = [
                'email' => $email,
                'amount' => intval($amount),
                'callback_url' => $callbackUrl,
                'reference' => $referenceId !== "" ? $referenceId : ""
            ];

            $fields_string = http_build_query($fields);
            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer {$this->SECRET_KEY}",
                "Cache-Control: no-cache",
            ));

            //So that curl_exec returns the contents of the cURL; rather than echoing it
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

            //execute post
            $result = json_decode(curl_exec($ch));
            return $result;
        }

        /**
         * @param string $referenceId
         * 
         * The Unique ID for the particular transaction.
         */

        function verifyTransaction (string $referenceId){
            $curl = curl_init();

            curl_setopt_array($curl, array(

                CURLOPT_URL => "https://api.paystack.co/transaction/verify/{$referenceId}",

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_ENCODING => "",

                CURLOPT_MAXREDIRS => 10,

                CURLOPT_TIMEOUT => 30,

                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

                CURLOPT_CUSTOMREQUEST => "GET",

                CURLOPT_HTTPHEADER => array(

                "Authorization: Bearer {$this->SECRET_KEY}",

                "Cache-Control: no-cache",

                ),

            ));

            
            $response = json_decode(curl_exec($curl));

            $err = curl_error($curl);

            curl_close($curl);

            

            if ($err) {

                echo "cURL Error #:" . $err;

            } else {
                return $response;

            }
        }
    }