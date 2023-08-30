<?php
namespace App\PaymentGateway;

//Your Data Here

class MonnifyPayment
{
    protected $authH;
    protected $accessToken;
    public $liveContractCode = 765974016458;
    public $testContractCode = 6648828487;
    private $apiLiveKey = 'MK_PROD_4XA24J4TGS';
    private $apiLiveSecret = 'GHLLG8N4N2PJYNAVKDCHYB9HQFAJNKQR';
    private $baseUrl = 'https://sandbox.monnify.com/';
    private $apiTestKey = 'MK_TEST_HACKGP20ZH';
    private $apiTestSecret = '34D8CHR1SBNAEDK4EG1ZZN8WWS6E9LPN';

    // this function is called everytime this class is instantiated
    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    private function generateToken()
    {
        $auth = base64_encode($this->apiTestKey . ':' . $this->apiTestSecret);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . "api/v1/auth/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $auth",
                "Content-Type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);
        return $response->responseBody->accessToken ?? '';
    }

    public function initTrans($data = [])
    {
        $newData = json_encode($data);
        $curl = curl_init();
        $auth = $this->authH;

        $token = $this->generateToken();

        try {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseUrl . 'api/v1/merchant/transactions/init-transaction',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $newData,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token",
                    "Content-Type: application/json",
                ),
            ));
            $response = curl_exec($curl);
            $response = json_decode($response);
            curl_close($curl);
            return $response->responseBody;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function verifyTrans($transRef)
    {
        $ref = urlencode($transRef);
        $accessToken = $this->accessToken;
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseUrl . 'api/v2/transactions/' . $ref . '',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            //Use bearer when dealing with oauth 2
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer $accessToken",
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            return ($res['responseBody']);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
