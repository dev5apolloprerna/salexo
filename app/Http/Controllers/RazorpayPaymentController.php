<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Razorpay\Api\Api;
use DB;
use Redirect,Response;
use Session;
use Exception;
use Illuminate\Support\Facades\Mail;
  
class RazorpayPaymentController extends Controller
{
     public function index($id)
    {
        $Order = Order::where("order_id",$id)->where(['iStatus'=>1,'isDelete'=>0])->first();
        
        $price = $Order->netAmount;
        
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $OrderAmount = $price * 100;
        $orderData = [
            'receipt'         => $id.'-'.date('dmYHis'),
            'amount'          => $OrderAmount ,
            'currency'        => 'INR',

        ];
        $razorpayOrder = $api->order->create($orderData);
        $orderId = $razorpayOrder['id'];
        $data = array(
            'order_id' => $orderId,
            'oid' => $id,
            'amount' => $price,
            'currency' => 'INR',
            'receipt' => $razorpayOrder['receipt'],
        );
        Payment::insert($data);
        // dd($Order); frontview.dataFrom
        return view('frontview.razorpayView',compact('Order','orderId'));
    }
    /*public function store(Request $request)
    {
        $input = $request->all();
  
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
  
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
  
        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 
  
            } catch (Exception $e) {
                return  $e->getMessage();
                Session::put('error',$e->getMessage());
                return redirect()->back();
            }
        }
          
        Session::put('success', 'Payment successful');
        return redirect()->back();
    }*/
    public function razorPaySuccess(Request $request)
    {
        $orderId = $request->orderId;
        $data = [

                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'razorpay_order_id' => $request->razorpay_order_id,

        ];
        Payment::where('order_id',$orderId)->update($data);
        $payment=Payment::where('order_id',$orderId)->first();
    
        $stringdata = $orderId.'|'.$request->razorpay_payment_id;
        $generated_signature = hash_hmac('sha256',$stringdata , env('RAZORPAY_SECRET'));
        $razorpay_signature = $request->razorpay_signature;
        if ($generated_signature == $razorpay_signature) {
            $updateData = Payment::where('order_id',$orderId)->update(['status'=>'Success','iPaymentType' => 1,'iSubscriptionYear' => 1,"Remarks" => "Online Payment"]);
            if($updateData)
            {
                $updateProfileData = array(
                    'isPayment' => 1
                );
                Order::where("order_id",$request->profile_id)->update($updateProfileData);
            }
            
            $Order = Order::select('order.*',DB::raw('(select states.name from states where order.shiiping_state=states.id limit 1) as state'))->where("order_id",$payment->oid)->first();
            $SendEmailDetails = DB::table('sendemaildetails')
                ->where(['id' => 9])
                ->first();
    
    
            $root = $_SERVER['DOCUMENT_ROOT'];
            $file = file_get_contents($root . '/sukti/mailers/checkoutmail.html', 'r');
            
            if($Order['discount']){
                    $discount=$Order['discount'];
                }else{
                    $discount=0;
                }
                
            $address= $Order['shiiping_address1'] .','. $Order['billAddress'];
            $file = str_replace('#name', $Order['shipping_cutomerName'], $file);
            $file = str_replace('#email', $Order['shipping_email'], $file);
            $file = str_replace('#mobile', $Order['shipping_mobile'], $file);
            $file = str_replace('#address', $address, $file);
            $file = str_replace('#state', $Order['state'], $file);
            $file = str_replace('#city', $Order['shipping_city'], $file);
            $file = str_replace('#pincode', $Order['shipping_pincode'], $file);
            $file = str_replace('#deliveryType', $Order['deliveryType'], $file);
            $file = str_replace('#amount', $Order['amount'], $file);
            $file = str_replace('#netAmount', $Order['netAmount'], $file);
            $file = str_replace('#discount', $discount, $file);
            $file = str_replace('#shipping_Charges', $Order['shipping_Charges'], $file);
            
            $html = "";
            $i=1;
            $iTotal = 0;
            $cartItems = \Cart::getContent();
            
            foreach ($cartItems as $cartItem) {
                $html .= '<tr>
                    <td style="text-align: center;">'.$i.'</td>
                    <td style="text-align: center;">'.$cartItem['name'].'</td>
                    <td style="text-align: center;"><img width="48" height="48" src='.$cartItem['attributes']['image'].'></td>
                    <td style="text-align: center;">'.$cartItem['weight'].'</td>
                    <td style="text-align: center;">'.$cartItem['quantity'].'</td>
                    <td style="text-align: center;">'.$cartItem['price'].'</td>
                    <td style="text-align: center;">'. $cartItem['price'] * $cartItem['quantity'] .'</td>

            
                </tr>';
            $i++;
            }
            $file = str_replace('#tableProductTr', $html, $file);

            $setting = DB::table("setting")->select('email')->first();
            $toMail = $setting->email;// "shahkrunal83@gmail.com";//
            
            $to = $toMail;
            $subject = "New Order";
            $message = $file;
            $header = "From:".$SendEmailDetails->strFromMail."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html\r\n";
            
            $retval = mail($to,$subject,$message,$header);
            
            
            $to = $Order['shipping_email'];
            $subject = "New Order";
            $message = $file;
            $header = "From:".$SendEmailDetails->strFromMail."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html\r\n";
            
            $retval = mail($to,$subject,$message,$header);
            
            \Cart::clear();
            return 1;
        }
        else{
            $updateData = Payment::where('order_id',$orderId)->update(['status'=>'Fail']);
            return 0;
            
        }

    }
     public function RazorThankYou()
    {
        return view('frontview.thankyouPage');
    }

    public function RazorFail()
    {
        return view('frontview.paymentFail');
    }
    
}