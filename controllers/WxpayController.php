<?php
namespace controllers;

use Yansongda\Pay\Pay;

class WxpayController
{
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',

        // 通知的地址
        'notify_url' => 'http://364bc406.ngrok.io/wxpay/notify',
    ];

    // 调用微信接口进行支付
    public function pay()
    {
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn);
        if($data['status']==0)
        {
            $order = [
                'out_trade_no' => $sn,
                'total_fee' => $data['money'] * 100, // **单位：分**
                'body' => '用户充值：'.$data['money'].'元',
            ];
    
            // 调用接口
            $pay = Pay::wechat($this->config)->scan($order);
            if($pay->result_code == 'SUCCESS' && $pay->return_code == 'SUCCESS')
            {
                view('users.wxpay',[
                    'code' => $pay->code_url,// 支付码
                    'sn' => $sn,
                ]);
            }

        }
        else
        {
            die('订单状态不允许进行支付操作~');
        }
    }

    public function notify()
    {
        // $log = new \libs\Log('wxpay');
        // $log->log('接收到微信的消息');
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            // $log->log('验证成功，接受的数据时：'.file_get_contents('php://input'));
            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                // 获取订单信息
                $order = new \models\Order;
               
                $orderInfo = $order->findBySn($data->out_trade_no);
                // 如果订单的状态为未支付状态 ，说明是第一次收到消息，更新订单状态 
                if($orderInfo['status'] == 0)
                {
                    // 开启事务
                    $order->startTrans();
                    // 设置订单为已支付状态
                    $ret1 = $order->setPaid($data->out_trade_no); 

                    // 更新用户余额
                    $user = new \models\User;
                    $ret2 = $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                
                    if($ret1 && $ret2)
                    {
                        // 提交事务
                        $order->commit();
                    }
                    else
                    {
                        // 回滚事务
                        $order->rollback();
                    }
                }
            }

        } catch (Exception $e) {
            // $log->log('验证失败！'. $e->getMessage() );
            var_dump( $e->getMessage() );
        }
        
        $pay->success()->send();
    }
}