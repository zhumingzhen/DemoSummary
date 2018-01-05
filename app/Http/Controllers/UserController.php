<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;
//use \Yunpian\Sdk\YunpianClient;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{

        protected $encode;
        /**
         *      * Create a new controller instance.
         *           *
         *                * @return void
         *                     */
        public function __construct()
        {
                        
        }

        public function login(Request $request){
            $mobile = $request->get('mobile');
            if ($mobile){
                $encode = $request->get('encode');
                $redisEncode = Redis::get('encode_'.$mobile);
                if ($encode != $redisEncode){
                    return self::echojson(40004,'验证码错误，请重试！');
                }
                if(empty($mobile)){
                    return self::echojson(40005,'手机号不能为空！');
                }
                $isUserByMobile = User::where('name',$mobile)->first();

                if($isUserByMobile){
                    $userData = User::where(['name'=>$mobile,'password'=>MD5('123456')])->first();
                    if($userData){
                        return self::echojson(20000,'手机快捷登录成功',$userData);
                    }else{
                        return self::echojson(40000,'手机快捷登录密码错误！');
                    }
                }else{
                    $input['name'] = $mobile;
                    $input['email'] = 'z@it1.me'.':'.time();
                    $input['password'] = MD5('123456');

                    $res = User::create($input);
                    if($res){
                        return self::echojson(20000,'手机快捷注册成功',$res);
                    }else{
                        return self::echojson(40000,'手机快捷注册失败，请稍后重试。');
                    }
                }
            }else{
                $name = $request->get('name');
                $password = MD5($request->get('password'));

                $isUserName = User::where('name',$name)->first();
                if(!$isUserName){
                    return self::echojson(40000,'账号不存在');
                }

                $userData = User::where(['name'=>$name,'password'=>$password])->first();
                if($userData){
                    return self::echojson(20000,'登录成功',$userData);
                }else{
                    return self::echojson(40000,'密码错误！');
                }
            }
        }

        private static function echojson($code,$msg,$data=''){
            if($data == ''){
                return json_encode(array('code'=>$code,'msg'=>$msg));
            }
            return json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$data));
        }

        public function register(Request $request){
            $input = $request->all();
            
            if(empty($input['name'])){
                return self::echojson(40005,'用户名不能为空！');
            }

            if(empty($input['password'])){
                return self::echojson(40005,'密码不能为空！');
            }

            $isUser = User::where('name',$input['name'])->first();

            if($isUser){
                return self::echojson(40002,'用户名已存在');
            }

            $input['email'] = 'z@it1.me'.':'.time();

            $input['password'] = MD5($input['password']); 

            $res = User::create($input);
            if($res){
                return self::echojson(20000,'注册成功',$res);
            }else{
                return self::echojson(40000,'注册失败，请稍后重试。');
            }

        }

        public function sendSms(Request $request){
            $mobile = $request->get('mobile');
            $encode = rand(1000,9999);
            Redis::set('encode_'.$mobile, $encode);
            Redis::expire('encode_'.$mobile, 300);

//            $clnt = YunpianClient::create('5c68c558dc020439d0826ce0c9135ecf');
//            $param = [YunpianClient::MOBILE => $mobile,YunpianClient::TEXT => '【指尖跳跃】感谢您注册指尖跳跃，您的验证码是'.$encode];
//            $r = $clnt->sms()->single_send($param);
//            var_dump($r->code);
//            exit;
            $config = [
                'accessKeyId'    => 'LTAIToh9bjqalPEr',
                'accessKeySecret' => 'bSYMO1Gugw1AG2mu1btY6sfAM6hOVc',
            ];

            $client  = new Client($config);
            $sendSms = new SendSms;
            $sendSms->setPhoneNumbers($mobile);
            $sendSms->setSignName('指尖跳跃');
            $sendSms->setTemplateCode('SMS_119081874');
            $sendSms->setTemplateParam(['code' => $encode]);

//            $res = $client->execute($sendSms);
            if ($res->Message == 'OK'){
                return self::echojson(20000,'验证码请求成功');
            }else{
                return self::echojson(40000,'验证码请求失败');
            }


        } 

}
