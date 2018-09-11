<?php
/* 支付宝支付的控制器 */
namespace controllers;

use Yansongda\Pay\Pay;
use models\Order;
class AlipayController
{
    // 配置
    public $config = [
        'app_id' => '2016091600527371',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz2Sc/EtzQFrozU0EnTDeuwtcKHHr3lWja40pY6RT/5DMkYwEamdupuJHWJxEJRVdOxTB3B6LMWJka6VMUnXg1VIzql/0+3mGgeEZB2Py1sVBzq9PDeEcInrXwp/peppPuaNJdsDbJgogg1JALjpP6bdlGxWvqR5LnbbDj/tAR6X+150gAfiR0PoMTP86HuSYDcmt5dpE+l4rLKe+cuGwJyOSzCvyIs2Nen481ffRKU2eOKrsR4or3BVGZW/+jsZkFG8PAPaygAXdx0fWvPY/UiVaM7WwhQiKWvM8KmjdVGvUIX1gA1ICTq0wNvE7dEaNGcgTyYc/mtk5HjWkEmQGKQIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEowIBAAKCAQEA2XP4V787f4aTf4KXUFpRA+UVH0MXcYJy2d91iMvZ4DyVm3p3q+qwn8D68PZtuyOd6mOXVP+/e/nS9s3NTmkYcxy7hNBiSl68dXpQCKTepPoSDpO3G/BPN4Qp3qHJwO0NjjhtDm6K4Rm96E3b0ILl9uokPOagsKcP8Y7p3MT6O9KSz+gNdU4MpHEQhiGKq7mICt4u2QXSeESFZYgyniu71OMNwcA6a33U27PmsuVAEQE2j4Jrp1kBplCzZPzWO8zYFMCEaytOoGW5CliR7cdXiH2XCdZzmj9jbXi2kunAUBfmkjfxNzk/G4SrHgioIn5hur/rtx6J65XAdTkdFhI2fQIDAQABAoIBAQCwIWHm2Os9WOjBoColmHIEgJoCL1qewzV5yaiuu8bm/MuFGsYxxq93Rl07ra6lpKy0/CPIYjpcFbdN1tZTi4aVPpGYex0R9fgaA48t4TTBVhgoHHd1NslDQ1aSkGMVVCzlpEiZJupwd2Q99Ep2coAH5hCiD3/adgbQ9pvwCNBSgLdg29ja3gfKa1HIzo2G69yH0F/VMi+IJeGVKHnluVsTCQU1hZks5xh0/BTzzH4/avEKQ/wM0mTXf4vkrpgSiAcZzp3/bAJYnw8n3KU1vPK8SYrrc8JsxljR/tx0sayCfc1vVubWS4MPvDwkd9gUDFEMuRruSQbCX+4gnyysTWY1AoGBAPflmO3cpGgxBBf4Z8Dt0rH+pB45sBy5Q57KYasmecnAWWKHNwlg2MAxcVqAvrRcr8Ck+x6EbBPhGaIhOGAqbGhuKsQVTx1EmdfztXDMcZxZsY/ZrlWCeFt2jcgYuNyexzXIBj1kSvgyfuv9J48oPbNhBnTtZZ3mx/Ba0Srm/13XAoGBAOCPnknTIMcSBvTqr2wvaQQk4qp+2XS+uMjOikyyrLp/WVv9eP0tMWJKMzi7KhIiwq8rYhpRAO9JAtTiW4MeCR/DuYWRCeXmi+h2biIUE6J5rT1HYRaDkb4w5Z/dm+cZ+D+lMD2GE1eRAgYsECPGpMsvUXrhaU4Jcxx1ELjv1/vLAoGAJmSSyNQRSmm8/pFkUEcFCLgtZtj8Y5Z12JPziHRPDGYT8eSLK5KqPynpKmEiKADq32Ut104fBv0n4SpP9uTbIVlemjvKovfK8900zqF7PwHNNEA8ddXdGh1EXCcoClM0+yldfgiYa9Q2QApXJB7RX4S5YUabJFdnw9vs5T4dcsUCgYA3feYMsjqgVukXLsNoxZJ67q6AmVYdTmAVZ0yvxVt/vqaTX+C9F6TjeBiORVoRHzvi06KrhCbp+q3Tc5hPn2V7zv8SbZP3lvAt4s42Z6WueckAopnwWVTznduwlK/I/Rbmi2iPW4l+Exxf8BWQ8a6Zczj8V6WRHi6u8pLOUJ6b3wKBgGNYQ0xqXX/3Jn0QLJmU6R08H1XsrfaChELyuzfkcS1TotbsbOJJ/kKX0oPZMBXGKAy7XYyVKxg7osC88urLY1CDF5W2AFr80O82K8yG/ktDBXK7/yNIhujb7ncYYlvEKTaWMvwCuqSV/XspMipmUl+jTfKpRX1+Z+Ge/xYsaooJ',
        
        // 通知地址
        'notify_url' => 'http://942334c4.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://localhost:9000/alipay/return',
        
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];

    // 跳转到支付宝
    public function pay()
    {
        $sn = $_POST['sn'];
        $model = new Order;
        // 根据订单编号sn 获取订单信息
        $data = $model->findBySn($sn);
        

        // 先在本地的数据库中生成一个订单（支付的金额、支付状态等信息、订单号）
        // 模拟一个假的订单
        if($data['status']==0)
        {
            $order = [
                'out_trade_no' => $sn,    // 本地订单ID
                'total_amount' => $data['money'],    // 支付金额（单位：元）
                'subject' => '用户充值：'.$data['money'].'元', // 支付标题
            ];

            // 跳转到支付宝
            $alipay = Pay::alipay($this->config)->web($order);
            $alipay->send();
        }
        else
        {
            die('订单状态不允许进行支付操作~');
        }
        
    }
    // 支付完成跳回
    public function return()
    {
        // 验证数据是否是支付宝发过来
        $data = Pay::alipay($this->config)->verify();


        echo '<h1>支付成功！</h1> <hr>';

        var_dump( $data->all() );

    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED' )
            {
                // 获取订单信息
                $order = new Order;
               
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
        
        } catch (\Exception $e) {
            die('Illegal information');
        }

        // 回应支付宝服务器（如何不回应，支付宝会一直重复给你通知）
        $alipay->success()->send();
    }

    // 退款
    public function refund()
    {
        // 生成唯一退款订单号（以后使用这个订单号，可以到支付宝中查看退款的流程）
        $refundNo = md5( rand(1,99999) . microtime() );

        try{
            $order = [
                'out_trade_no' => '1536311811',    // 退款的本地订单号
                'refund_amount' => 0.01,              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 生成 的退款订单号
            ];

            // 退款
            $ret = Pay::alipay($this->config)->refund($order);

            if($ret->code == 10000)
            {
                echo '退款成功！';
            }
            else
            {
                echo '失败' ;
                var_dump($ret);
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}