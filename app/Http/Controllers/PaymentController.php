<?php

namespace App\Http\Controllers;

use App\Models\UserMembership;
use App\Models\MembershipPackage;
use App\Models\User;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Services\ZaloPayService;

class PaymentController extends Controller
{
    private $vnp_TmnCode = "Z4TFCUTA";
    private $vnp_HashSecret = "YZH3Y12YNHEMI6FKI7AIEANDEJVG0QIM";
    private $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

    private $zaloPayService;

    public function __construct(ZaloPayService $zaloPayService)
    {
        $this->zaloPayService = $zaloPayService;
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'duration' => 'required|in:1,3,6,12',
            'payment_method' => 'required|in:vnpay,momo,zalopay,bank'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để tiếp tục'], 401);
        }

        $package = MembershipPackage::findOrFail($request->package_id);
        $amount = $package->price * $request->duration;

        // Xử lý thanh toán theo phương thức
        switch ($request->payment_method) {
            case 'vnpay':
                // Tạo transaction mới
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->package_id = $package->id;
                $transaction->duration = $request->duration;
                $transaction->amount = $amount;
                $transaction->status = 'pending';
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
                return $this->createVNPay($transaction);
            case 'momo':
                // Tạo transaction mới
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->package_id = $package->id;
                $transaction->duration = $request->duration;
                $transaction->amount = $amount;
                $transaction->status = 'pending';
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
                return $this->createMoMo($transaction);
            case 'zalopay':
                // Không tạo transaction ở đây, chỉ gọi sang createZaloPay
                return $this->createZaloPay($request);
            case 'bank':
                // Tạo transaction mới
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->package_id = $package->id;
                $transaction->duration = $request->duration;
                $transaction->amount = $amount;
                $transaction->status = 'pending';
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
                return $this->createBankTransfer($transaction);
            default:
                return response()->json(['message' => 'Phương thức thanh toán không được hỗ trợ'], 400);
        }
    }

    public function createVNPay(Transaction $transaction)
    {
        try {
            $vnp_Returnurl = url('/payment/vnpay/return?transaction_id=' . $transaction->id);
            
            $vnp_TxnRef = $transaction->id;
            $vnp_OrderInfo = 'Thanh toan goi thanh vien #' . $transaction->id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = (int)($transaction->amount * 100);
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip();
            $vnp_CreateDate = date('YmdHis');

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $this->vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            // Log thông tin request
            Log::info('VNPay payment request:', [
                'transaction_id' => $transaction->id,
                'amount' => $vnp_Amount,
                'return_url' => $vnp_Returnurl,
                'payment_url' => $vnp_Url
            ]);

            return response()->json(['url' => $vnp_Url]);
        } catch (\Exception $e) {
            Log::error('Error creating VNPay payment:', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Lỗi khi tạo giao dịch VNPay: ' . $e->getMessage()], 500);
        }
    }

    public function createMoMo(Transaction $transaction)
    {
        try {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = 'MOMOBKUN20180529';
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            
            $orderInfo = "Thanh toan goi thanh vien #{$transaction->id}";
            $orderId = $transaction->id . '_' . time();
            $amount = (int)($transaction->amount);
            $ipnUrl = url('/api/payment/callback/momo');
            $redirectUrl = url('/payment/success?transaction_id=' . $transaction->id);
            $extraData = "";
            $requestId = time() . "";
            $requestType = "payWithMethod";

            // Tạo chuỗi rawHash theo định dạng của MoMo
            $rawHash = "accessKey=" . $accessKey .
                      "&amount=" . $amount .
                      "&extraData=" . $extraData .
                      "&ipnUrl=" . $ipnUrl .
                      "&orderId=" . $orderId .
                      "&orderInfo=" . $orderInfo .
                      "&partnerCode=" . $partnerCode .
                      "&redirectUrl=" . $redirectUrl .
                      "&requestId=" . $requestId .
                      "&requestType=" . $requestType;

            $signature = hash_hmac('sha256', $rawHash, $secretKey);

            // Chuẩn bị dữ liệu gửi đi
            $data = [
                'partnerCode' => $partnerCode,
                'partnerName' => "Test",
                "storeId"     => "MomoTestStore",
                'requestId'   => $requestId,
                'amount'      => $amount,
                'orderId'     => $orderId,
                'orderInfo'   => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl'      => $ipnUrl,
                'lang'        => 'vi',
                'extraData'   => $extraData,
                'requestType' => $requestType,
                'signature'   => $signature
            ];

            Log::info('MoMo payment request data:', $data);
            Log::info('MoMo raw hash string:', ['rawHash' => $rawHash]);

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }
            
            curl_close($ch);

            Log::info('MoMo API response:', [
                'http_code' => $httpCode,
                'response' => $result
            ]);

            if ($httpCode !== 200) {
                throw new \Exception('MoMo API returned status code: ' . $httpCode . ' with response: ' . $result);
            }

            $jsonResult = json_decode($result, true);
            
            if (!isset($jsonResult['payUrl'])) {
                throw new \Exception('Invalid response from MoMo: ' . $result);
            }

            // Lưu orderId vào transaction để sử dụng khi xử lý callback
            $transaction->payment_info = [
                'momo_order_id' => $orderId
            ];
            $transaction->save();

            return response()->json(['url' => $jsonResult['payUrl']]);
        } catch (\Exception $e) {
            Log::error('Error creating MoMo payment:', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Lỗi khi tạo giao dịch MoMo: ' . $e->getMessage()], 500);
        }
    }

    public function createZaloPay(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:membership_packages,id',
            'duration' => 'required|in:1,3,6,12'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vui lòng đăng nhập để tiếp tục'], 401);
        }

        $package = MembershipPackage::findOrFail($request->package_id);
        $amount = $package->price * $request->duration;

        // Kiểm tra transaction pending đã tồn tại chưa
        $existing = Transaction::where([
            ['user_id', $user->id],
            ['package_id', $package->id],
            ['duration', $request->duration],
            ['payment_method', 'zalopay'],
            ['status', 'pending']
        ])->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã có giao dịch đang chờ xử lý cho gói này!'
            ], 400);
        }

        // Tạo transaction mới
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'duration' => $request->duration,
            'amount' => $amount,
            'payment_method' => 'zalopay',
            'status' => 'pending',
            'payment_info' => [
                'current_package_id' => $request->current_package_id,
                'is_upgrade' => $request->is_upgrade
            ]
        ]);

        try {
            $result = $this->zaloPayService->createPayment(
                $amount,
                "Thanh toan goi thanh vien {$package->name}",
                $user,
                $transaction
            );

            // Cập nhật thông tin thanh toán vào transaction
            $transaction->payment_info = array_merge($transaction->payment_info ?? [], $result['payment_info']);
            $transaction->save();

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'order_id' => $result['order_id'],
                'qr_code' => $result['qr_code'],
                'transaction_id' => $result['transaction_id']
            ]);

        } catch (\Exception $e) {
            // Cập nhật trạng thái transaction
            $transaction->status = 'failed';
            $transaction->payment_info = array_merge($transaction->payment_info ?? [], [
                'error' => $e->getMessage(),
                'error_time' => now()
            ]);
            $transaction->save();

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo giao dịch ZaloPay: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createBankTransfer(Transaction $transaction)
    {
        $bankInfo = [
            'bank_name' => 'Vietcombank',
            'account_number' => '1234567890',
            'account_name' => 'CONG TY ABC',
            'branch' => 'Chi nhánh Hà Nội',
            'transfer_content' => "TMV_{$transaction->id}"
        ];

        $transaction->payment_info = $bankInfo;
        $transaction->save();

        return response()->json([
            'bank_info' => $bankInfo,
            'amount' => $transaction->amount,
            'transaction_id' => $transaction->id
        ]);
    }

    public function vnpayReturn(Request $request)
    {
        try {
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = array();
            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $hashData = "";
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
            $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

            if ($secureHash != $vnp_SecureHash) {
                Log::error('Invalid VNPay signature', [
                    'received' => $vnp_SecureHash,
                    'calculated' => $secureHash
                ]);
                return view('payment.fail');
            }

            if ($request->vnp_ResponseCode == '00') {
                return $this->completeTransaction($request->vnp_TxnRef);
            } else {
                Log::error('VNPay payment failed', [
                    'response_code' => $request->vnp_ResponseCode,
                    'message' => $request->vnp_Message ?? 'Unknown error'
                ]);
                return view('payment.fail');
            }
        } catch (\Exception $e) {
            Log::error('Error processing VNPay return:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return view('payment.fail');
        }
    }

    public function handleMoMoCallback(Request $request)
    {
        try {
            $partnerCode = $request->partnerCode;
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            $requestId = $request->requestId;
            $amount = $request->amount;
            $orderId = $request->orderId;
            $orderInfo = $request->orderInfo;
            $orderType = $request->orderType;
            $transId = $request->transId;
            $resultCode = $request->resultCode;
            $message = $request->message;
            $payType = $request->payType;
            $signature = $request->signature;
            $responseTime = $request->responseTime;
            $extraData = $request->extraData ?? "";

            // Tạo chuỗi rawHash
            $rawHash = "accessKey=" . $accessKey .
                      "&amount=" . $amount .
                      "&extraData=" . $extraData .
                      "&orderId=" . $orderId .
                      "&orderInfo=" . $orderInfo .
                      "&partnerCode=" . $partnerCode .
                      "&requestId=" . $requestId .
                      "&requestType=payWithMethod";

            $expectedSignature = hash_hmac('sha256', $rawHash, $secretKey);

            // Tìm transaction dựa trên orderId
            $transaction = Transaction::where('payment_info->momo_order_id', $orderId)->first();
            
            if (!$transaction) {
                return view('payment.fail');
            }

            if ($resultCode !== '0') {
                return view('payment.fail');
            }

            // Cập nhật thông tin thanh toán
            $transaction->payment_info = array_merge($transaction->payment_info ?? [], [
                'momo_trans_id' => $transId,
                'momo_pay_type' => $payType,
                'momo_result_code' => $resultCode,
                'momo_message' => $message,
                'momo_response_time' => $responseTime
            ]);
            $transaction->save();

            // Hoàn tất giao dịch và chuyển hướng đến trang thành công
            return $this->completeTransaction($transaction->id);
        } catch (\Exception $e) {
            return view('payment.fail');
        }
    }

    public function handleZaloPaySuccess(Request $request)
    {
        try {
            Log::info('Received ZaloPay callback:', $request->all());

            $result = $this->zaloPayService->verifyCallback($request->all());

            if (!$result['is_valid']) {
                Log::error('Invalid ZaloPay callback signature', [
                    'received' => $request->input('checksum'),
                    'data' => $result['data'],
                    'request_data' => $request->all()
                ]);
                return view('payment.fail');
            }

            // Tìm transaction dựa trên apptransid
            $transaction = Transaction::where('payment_info->zalopay_app_trans_id', $result['data']['app_trans_id'])->first();

            if (!$transaction) {
                Log::error('Transaction not found for ZaloPay callback', [
                    'app_trans_id' => $result['data']['app_trans_id'],
                    'data' => $result['data']
                ]);
                return view('payment.fail');
            }

            // Cập nhật trạng thái giao dịch
            if ($result['data']['status'] == 1) {
                $transaction->status = 'completed';
                $transaction->save();

                // Cập nhật thời hạn thành viên
                $this->updateMembershipExpiry($transaction);

                Log::info('ZaloPay payment completed successfully', [
                    'transaction_id' => $transaction->id
                ]);

                return view('payment.success');
            } else {
                $transaction->status = 'failed';
                $transaction->save();

                Log::info('ZaloPay payment failed', [
                    'transaction_id' => $transaction->id,
                    'status' => $result['data']['status']
                ]);

                return view('payment.fail');
            }

        } catch (\Exception $e) {
            Log::error('Error processing ZaloPay callback:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('payment.fail');
        }
    }

    public function handlePaymentSuccess(Request $request)
    {
        try {
            // Lấy transaction_id từ query string
            $transactionId = $request->query('transaction_id');
            
            if (!$transactionId) {
                Log::error('No transaction ID found in success URL');
                return view('payment.fail');
            }

            // Tìm transaction
            $transaction = Transaction::find($transactionId);
            
            if (!$transaction) {
                Log::error('Transaction not found', ['transaction_id' => $transactionId]);
                return view('payment.fail');
            }

            // Nếu transaction đã completed thì không cần xử lý nữa
            if ($transaction->status === 'completed') {
                return view('payment.success');
            }

            // Hoàn tất giao dịch
            return $this->completeTransaction($transactionId);

        } catch (\Exception $e) {
            Log::error('Error handling payment success:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('payment.fail');
        }
    }

    protected function updateMembershipExpiry(Transaction $transaction)
    {
        try {
            $user = User::findOrFail($transaction->user_id);
            $package = MembershipPackage::findOrFail($transaction->package_id);

            // Tìm gói thành viên đang active của cùng loại
            $existingMembership = UserMembership::where([
                'user_id' => $user->id,
                'membership_package_id' => $package->id,
                'status' => 'active'
            ])->first();

            if ($existingMembership) {
                // Nếu đã có gói active, cộng thêm thời gian
                $currentEndDate = $existingMembership->end_at;
                $additionalDays = $package->duration_days * $transaction->duration;
                
                // Nếu gói đã hết hạn, lấy thời gian hiện tại làm điểm bắt đầu
                if ($currentEndDate < now()) {
                    $currentEndDate = now();
                }
                
                $existingMembership->end_at = $currentEndDate->addDays($additionalDays);
                $existingMembership->save();

                Log::info('Existing membership extended', [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'membership_id' => $existingMembership->id,
                    'old_end_at' => $currentEndDate,
                    'new_end_at' => $existingMembership->end_at,
                    'additional_days' => $additionalDays
                ]);

                return $existingMembership;
            }

            // Nếu chưa có gói active, tạo gói mới
            $membership = new UserMembership();
            $membership->user_id = $user->id;
            $membership->membership_package_id = $package->id;
            $membership->start_at = now();
            $membership->end_at = now()->addDays($package->duration_days * $transaction->duration);
            $membership->status = 'active';
            $membership->payment_id = $transaction->id;
            $membership->amount_paid = $transaction->amount;
            $membership->payment_method = $transaction->payment_method;
            $membership->save();

            Log::info('New membership created', [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'membership_id' => $membership->id,
                'start_at' => $membership->start_at,
                'end_at' => $membership->end_at,
                'status' => $membership->status
            ]);

            return $membership;
        } catch (\Exception $e) {
            Log::error('Error processing membership:', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function completeTransaction($transactionId)
    {
        return DB::transaction(function () use ($transactionId) {
            try {
                $transaction = Transaction::findOrFail($transactionId);
                
                if ($transaction->status === 'completed') {
                    return view('payment.success');
                }

                $transaction->status = 'completed';
                $transaction->completed_at = now();
                $transaction->save();

                $package = MembershipPackage::findOrFail($transaction->package_id);
                $user = User::findOrFail($transaction->user_id);

                // Tạo gói thành viên mới
                $membership = new UserMembership();
                $membership->user_id = $user->id;
                $membership->membership_package_id = $package->id;
                $membership->start_at = now();
                $membership->end_at = now()->addDays($package->duration_days * $transaction->duration);
                $membership->status = 'active';
                $membership->payment_id = $transactionId;
                $membership->amount_paid = $transaction->amount;
                $membership->payment_method = $transaction->payment_method;
                $membership->save();

                // Log thông tin tạo mới
                Log::info('New membership created', [
                    'transaction_id' => $transactionId,
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'package_name' => $package->name,
                    'duration' => $transaction->duration,
                    'amount' => $transaction->amount,
                    'start_at' => $membership->start_at,
                    'end_at' => $membership->end_at,
                    'status' => $membership->status
                ]);

                // Kiểm tra sau khi tạo
                $createdMembership = UserMembership::find($membership->id);
                Log::info('Created membership verification', [
                    'membership_id' => $createdMembership->id,
                    'exists' => (bool)$createdMembership,
                    'status' => $createdMembership->status,
                    'end_at' => $createdMembership->end_at
                ]);

                return view('payment.success');
            } catch (\Exception $e) {
                Log::error('Error completing transaction', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    protected function createMembership(Transaction $transaction)
    {
        // Implementation of createMembership method
    }
} 