<?php

namespace App\PaymentGateway;

use App\SmParent;
use App\SmStudent;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Fees\Entities\FmFeesTransaction;
use Modules\Fees\Http\Controllers\FeesController;
use Modules\Lms\Entities\CoursePurchaseLog;
use Modules\Wallet\Entities\WalletTransaction;

class HandleMonnify
{

    public $monnify;

    public function __construct()
    {
        $this->monnify = new MonnifyPayment();
    }

    public function handle($data)
    {

        // try {
        $monnifyData = [];
        $email = "";
        $name = "";
        if ($data['type'] == "Fees") {
            $student = SmStudent::find($data['student_id']);
            if (!($student->email)) {
                $parent = SmParent::find($student->parent_id);
                $email = $parent->guardians_email;
                $name = ($parent->fathers_name) ? $parent->fathers_name : $parent->mothers_name;
            } else {
                $email = $student->email;
                $name = $student->full_name;
            }
        } elseif ($data['type'] == "Wallet" || $data['type'] == "Lms") {
            $user = User::find($data['user_id']);
            $email = $user->email;
            $name = $user->full_name;
        }

        // $paystack_info = SmPaymentGatewaySetting::where('gateway_name', 'Paystack')
        //     ->where('school_id', Auth::user()->school_id)
        //     ->first();

        // if (!$paystack_info || !$paystack_info->gateway_secret_key) {
        //     Toastr::warning('Paystack Credentials Can Not Be Blank', 'Warning');
        //     return redirect()->send()->back();
        // }

        // Config::set('paystack.publicKey', $paystack_info->gateway_publisher_key);
        // Config::set('paystack.secretKey', $paystack_info->gateway_secret_key);
        // Config::set('paystack.merchantEmail', $paystack_info->gateway_username);

        // {
        //   "amount": 100.00,
        //   "customerName": "Stephen Ikhane",
        //   "customerEmail": "stephen@ikhane.com",
        //   "paymentReference": "123031klsadkad",
        //   "paymentDescription": "Trial transaction",
        //   "currencyCode": "NGN",
        //   "contractCode":"32904822812",
        //   "redirectUrl": "https://my-merchants-page.com/transaction/confirm",
        //   "paymentMethods":["CARD","ACCOUNT_TRANSFER"]
        // }
        if ($data['type'] == "Wallet") {
            Session::put('payment_type', "Wallet");
            Session::put('amount', $data['amount']);
            Session::put('payment_mode', "Monnify");
            Session::put('wallet_type', $data['wallet_type']);
            $monnifyData = [
                "amount" => intval($data['amount']),
                "customerEmail" => $email,
                "customerName" => $name,
                "paymentReference" => uniqid(),
                "paymentDescription" => "Trial transaction",
                "redirectUrl" => env('APP_URL') . '/payment_gateway_success_callback/Monnify',
                "currencyCode" => 'NGN',
                "contractCode" => $this->monnify->testContractCode,
                "paymentMethods" => [
                    "CARD",
                    "ACCOUNT_TRANSFER",
                    "USSD",
                    "PHONE_NUMBER",
                ],
            ];

        } elseif ($data['type'] == "Fees") {
            Session::forget('amount');
            Session::put('payment_type', $data['type']);
            Session::put('invoice_id', $data['invoice_id']);
            Session::put('amount', $data['amount']);
            Session::put('payment_method', $data['payment_method']);
            Session::put('transcation_id', $data['transcationId']);

            $monnifyData = [
                "amount" => intval($data['amount']),
                "customerEmail" => $email,
                "customerName" => $name,
                "paymentReference" => uniqid(),
                "paymentDescription" => "Trial transaction",
                "redirectUrl" => env('APP_URL') . '/payment_gateway_success_callback/Monnify',
                "currencyCode" => 'NGN',
                "contractCode" => $this->monnify->testContractCode,
                "paymentMethods" => [
                    "CARD",
                    "ACCOUNT_TRANSFER",
                ],
            ];
        } elseif ($data['type'] == "Lms") {
            Session::put('payment_type', "Lms");
            Session::put('amount', $data['amount'] * 100);
            Session::put('payment_mode', "Monnify");
            Session::put('purchase_log_id', $data['purchase_log_id']);
            $monnifyData = [
                "amount" => intval($data['amount']),
                "customerEmail" => $email,
                "customerName" => $name,
                "paymentReference" => uniqid(),
                "paymentDescription" => "Trial transaction",
                "redirectUrl" => env('APP_URL') . '/payment_gateway_success_callback/Monnify',
                "currencyCode" => 'NGN',
                "contractCode" => $this->monnify->testContractCode,
                "paymentMethods" => [
                    "CARD",
                    "ACCOUNT_TRANSFER",
                    "USSD",
                    "PHONE_NUMBER",
                ],
            ];

        }

        // dd($monnifyData);
        $url = $this->monnify->initTrans($monnifyData);

        // dd($url);

        return $url->checkoutUrl;
        // } catch (\Exception $e) {
        //     Log::info($e->getMessage());
        //     Toastr::error('Operation Failed', 'Failed');
        //     return redirect()->send()->back();
        // }
    }
//url = url + payment_gateway_success_callback/Monnify
    public function successCallBack()
    {
        // dd("hello");
        $user = Auth::User();
        DB::beginTransaction();
        // try {
        $user = Auth::User();
        $walletType = Session::get('wallet_type');
        $amount = Session::get('amount');

        if (Session::get('payment_type') == "Wallet") {
            $addPayment = new WalletTransaction();
            $addPayment->amount = $amount;
            $addPayment->payment_method = "Monnify";
            $addPayment->user_id = $user->id;
            $addPayment->type = $walletType;
            $addPayment->school_id = Auth::user()->school_id;
            $addPayment->academic_id = getAcademicId();
            $addPayment->status = 'approve';
            $result = $addPayment->save();
            if ($result) {
                $user = User::find($user->id);
                $currentBalance = $user->wallet_balance;
                $user->wallet_balance = $currentBalance + $amount;
                $user->update();
                $gs = generalSetting();
                $compact['full_name'] = $user->full_name;
                $compact['method'] = $addPayment->payment_method;
                $compact['create_date'] = date('Y-m-d');
                $compact['school_name'] = $gs->school_name;
                $compact['current_balance'] = $user->wallet_balance;
                $compact['add_balance'] = $amount;

                @send_mail($user->email, $user->full_name, "wallet_approve", $compact);
            }
            DB::commit();

            Session::forget('payment_type');
            Session::forget('amount');
            Session::forget('payment_mode');
            Session::forget('wallet_type');

            return redirect()->route('wallet.my-wallet');
        } elseif (Session::get('payment_type') == "Fees") {
            $transcation = FmFeesTransaction::find(Session::get('transcation_id'));
            // dd(Session::get('fees_payment_id'));
            $addAmount = new FeesController;
            $addAmount->addFeesAmount($transcation->id, $amount);

            DB::commit();

            Session::forget('amount');
            Session::forget('payment_type');
            Session::forget('invoice_id');
            Session::forget('amount');
            Session::forget('payment_method');
            Session::forget('transcation_id');

            Toastr::success('Operation successful', 'Success');
            return redirect()->to(url('fees/student-fees-list', $transcation->student_id));
        } elseif (Session::get('payment_type') == "Lms") {
            if (Session::get('purchase_log_id')) {
                $coursePurchase = CoursePurchaseLog::find(Session::get('purchase_log_id'));
                $coursePurchase->active_status = 1;
                $coursePurchase->save();
                DB::commit();
                return redirect('lms/student/purchase-log');
            }
            Session::forget('payment_type');
        }
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     Toastr::error('Operation Failed Hello', 'Failed');
        //     return redirect()->send()->back();
        // }
    }

    public function cancelCallBack(Request $request)
    {
        dd($request->all());
    }
}
