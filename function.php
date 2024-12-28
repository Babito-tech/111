<?php
// +----------------------------------------------------------------------
// | XXXPHP框架 [ XXXPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 XXXPHP [ http://www. xxx.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://xxx.xxx
// +----------------------------------------------------------------------

// 为方便系统核心升级，二次开发中需要用到的公共函数请写在这个文件，不要去修改common.php文件

use think\Db;
use think\Container;
use think\facade\Env;
use app\user\model\User;
use app\user\model\UserMoneyType;

//判断是否是手机端浏览
function is_mobile() {
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = [
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
    ];
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取浏览器
 * @return   [type]                   [description]
 */
function getBrowser() {
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
    {
        return "ie";
    } else if (strpos($agent, 'Firefox') !== false) {
        return "firefox";
    } else if (strpos($agent, 'Chrome') !== false) {
        return "chrome";
    } else if (strpos($agent, 'Opera') !== false) {
        return 'opera';
    } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
        return 'safari';
    } else if (strpos($agent, 'MicroMessenger') !== false) {
        return 'weixin';
    } else {
        return 'unknown';
    }

}

/**
 * 获取浏览器版本号
 * @return   [type]                   [description]
 */
function getBrowserVer() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        //当浏览器没有发送访问者的信息的时候
        return 'unknow';
    }
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs)) {
        return $regs[1];
    } else if (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs)) {
        return $regs[1];
    } else if (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs)) {
        return $regs[1];
    } else if (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs)) {
        return $regs[1];
    } else if ((strpos($agent, 'Chrome') == false) && preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs)) {
        return $regs[1];
    } else {
        return 'unknow';
    }

}

/**
 * 同理获取访问用户的浏览器的信息
 * @return string
 */
function getOs() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $os = false;

    if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
        $os = 'Windows 95';
    } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
        $os = 'Windows ME';
    } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
        $os = 'Windows 98';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
        $os = 'Windows Vista';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
        $os = 'Windows 7';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
        $os = 'Windows 8';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
        $os = 'Windows 10'; #添加win10判断
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
        $os = 'Windows XP';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
        $os = 'Windows 2000';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
        $os = 'Windows NT';
    } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
        $os = 'Windows 32';
    } else if (preg_match('/linux/i', $agent)) {
        $os = 'Linux';
    } else if (preg_match('/unix/i', $agent)) {
        $os = 'Unix';
    } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'SunOS';
    } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'IBM OS/2';
    } else if (preg_match('/Mac/i', $agent) && preg_match('/10_12_2/i', $agent)) {
        $os = 'Mac OS X 10_12_2';
    } else if (preg_match('/Mac/i', $agent)) {
        $os = 'Mac OS X';
    } else if (preg_match('/PowerPC/i', $agent)) {
        $os = 'PowerPC';
    } else if (preg_match('/AIX/i', $agent)) {
        $os = 'AIX';
    } else if (preg_match('/HPUX/i', $agent)) {
        $os = 'HPUX';
    } else if (preg_match('/NetBSD/i', $agent)) {
        $os = 'NetBSD';
    } else if (preg_match('/BSD/i', $agent)) {
        $os = 'BSD';
    } else if (preg_match('/OSF1/i', $agent)) {
        $os = 'OSF1';
    } else if (preg_match('/IRIX/i', $agent)) {
        $os = 'IRIX';
    } else if (preg_match('/FreeBSD/i', $agent)) {
        $os = 'FreeBSD';
    } else if (preg_match('/teleport/i', $agent)) {
        $os = 'teleport';
    } else if (preg_match('/flashget/i', $agent)) {
        $os = 'flashget';
    } else if (preg_match('/webzip/i', $agent)) {
        $os = 'webzip';
    } else if (preg_match('/offline/i', $agent)) {
        $os = 'offline';
    } else {
        $os = 'unknow';
    }
    return $os;

}

//统计相关
function census_records($user_id, $how, $money, $addtime = "")
{
    $rel= app\census\model\CensusLog::add_census_log($user_id, $how, $money, $addtime);
    return($rel);
}

//统计相关
function census_month_records($user_id, $how, $money, $addtime = "")
{
    $rel= app\census\model\CensusLog::add_census_month_log($user_id, $how, $money, $addtime);
    return($rel);
}

//统计相关
function census_records_order($user_id, $how, $money, $order_id, $addtime = "")
{
    $rel= app\census\model\CensusOrderLog::add_census_log($user_id, $how, $money, $order_id, $addtime);
    return($rel);
}

/**
 * 获取二维码
 * @param $url string 二维码链接
 * @param $file_name string 文件名
 * @param $dir string 文件夾
 * @return string
 */
function get_qr_code($url,$file_name,$dir)
{
    if (!file_exists($dir)) {
        mkdir($dir,0777,true);
    }

    $file=$dir.$file_name.".png";
    if(@fopen($file, 'r' ) )
    {
        return $file;
    }
    $qrcode = new \QRcode();
    $value = $url; //二维码内容
    $errorCorrectionLevel = 'L';//容错级别
    $matrixPointSize = 6;//生成图片大小
    //设置生成的路径和名字
    $qrcode->png($value, $file, $errorCorrectionLevel, $matrixPointSize, 2);
    return $file;
}

/**
 * 获取海报
 * @param string $url 二维码内容(链接)
 * @param string $file_name 文件名
 * @param string $weixin 后缀
 * @return string
 */
function get_share_img($url,$file_name,$weixin='',$dir ="uploads/share/share_img/")
{
    $file= $dir.$file_name.$weixin.".png";
    if(@fopen($file, 'r' ) ) {
        return $file;
    }

    $imageDefault = array(
        'left'=>300,
        'top'=>1000,
        'right'=>0,
        'bottom'=>0,
        'width'=>150,
        'height'=>150,
        'opacity'=>100,
        'stream'=>0
    );

    $textDefault = array(
        /* 'text'=>"加微信：".$weixin,*/
        'text'=>"",
        'left'=>200,
        'top'=>800,
        'fontSize'=>25,       //字号
        'fontColor'=>'255,255,255', //字体颜色
        'angle'=>0,
    );

    //    $background = 'uploads/images/20200928/5343f85b9545fc4d8799ff13a2fb8f34.jpg';//海报背景
    $share_img = Db::name('share_img')
        ->where('status',1)
        ->order('id desc')
        ->value('share_img');//获取最新分享海报
    $background=Config('uploads_img_url').get_file_path($share_img);
    $config['image'][]['url'] = get_qr_code($url,$this->uid,$dir);
    $config['text'][]['fontPath'] = 'MSYH.TTF';
    $filename = $file;
    getbgqrcode($imageDefault,$textDefault,$background,$filename,$config);
    return $filename;
}

/**
 * 模拟http请求
 * @param $url string 請求地址
 * @param $data null post请求的数据 get 请求不需要传送
 * @return bool|string
 */
function http_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if(!empty($data)){
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 百度短网址接口
 * @param string $url 需要转换为短网址的原始网址
 * @return string | boolean 成功时返回短网址，失败时返回false
 */
function urlShorten($url=''){

    $cxt=stream_context_create(
        array(
            'http'=>array(
                'method'=>'POST',
                'header'=>array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
                'content'=>'url='.$url,
                'timeout'=>1
            )
        )
    );
    $rsp=@file_get_contents('http://dwz.cn/create.php',0,$cxt);
    if($rsp){
        if($json=json_decode($rsp)){
            if($json->status===0){
                return $json->tinyurl;
            }
        }
    }
    return false;
}

function createInvitecode()
{
    $str = range('A', 'Z');// 去除大写的O，以防止与0混淆
    unset($str[array_search('O', $str)]);
    $arr = array_merge(range(0, 9), $str);
    shuffle($arr);
    $invitecode = '';
    $arr_len = count($arr);
    for ($i = 0; $i < 6; $i++)
    {
        $rand = mt_rand(0, $arr_len - 1);
        $invitecode .= $arr[$rand];
    }
    return $invitecode;
}

function getbgqrcode($imageDefault,$textDefault,$background,$filename="",$config=array()){

    //如果要看报什么错，可以先注释调这个header
    if(empty($filename)) header("content-type: image/png");
    //背景方法
    $backgroundInfo = getimagesize($background);
    $ext = image_type_to_extension($backgroundInfo[2], false);
    $backgroundFun = 'imagecreatefrom'.$ext;
    $background = $backgroundFun($background);
    $backgroundWidth = imagesx($background);  //背景宽度
    $backgroundHeight = imagesy($background);  //背景高度
    $imageRes = imageCreatetruecolor($backgroundWidth,$backgroundHeight);
    $color = imagecolorallocate($imageRes, 0, 0, 0);
    imagefill($imageRes, 0, 0, $color);
    imagecopyresampled($imageRes,$background,0,0,0,0,imagesx($background),imagesy($background),imagesx($background),imagesy($background));
    //处理了图片
    if(!empty($config['image'])){
        foreach ($config['image'] as $key => $val) {
            $val = array_merge($imageDefault,$val);
            $info = getimagesize($val['url']);
            $function = 'imagecreatefrom'.image_type_to_extension($info[2], false);
            if($val['stream']){
                //如果传的是字符串图像流
                $info = getimagesizefromstring($val['url']);
                $function = 'imagecreatefromstring';
            }
            $res = $function($val['url']);
            $resWidth = $info[0];
            $resHeight = $info[1];
            //建立画板 ，缩放图片至指定尺寸
            $canvas=imagecreatetruecolor($val['width'], $val['height']);
            imagefill($canvas, 0, 0, $color);
            //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
            imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'],$resWidth,$resHeight);
            $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']) - $val['width']:$val['left'];
            $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']) - $val['height']:$val['top'];
            //放置图像
            imagecopymerge($imageRes,$canvas, $val['left'],$val['top'],$val['right'],$val['bottom'],$val['width'],$val['height'],$val['opacity']);//左，上，右，下，宽度，高度，透明度
        }
    }
    //处理文字

    if(!empty($config['text'])){

        foreach ($config['text'] as $key => $val) {
            $val = array_merge($textDefault,$val);
            list($R,$G,$B) = explode(',', $val['fontColor']);
            $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
            $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']):$val['left'];
            $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']):$val['top'];
            imagettftext($imageRes,$val['fontSize'],$val['angle'],$val['left'],$val['top'],$fontColor,$val['fontPath'],$val['text']);
        }
    }
    //生成图片
    if(!empty($filename)){
        $res = imagejpeg ($imageRes,$filename,90);
        //保存到本地
        imagedestroy($imageRes);
    }else{
        imagejpeg ($imageRes);
        //在浏览器上显示
        imagedestroy($imageRes);
    }

}

/**
 * 隐藏真实姓名
 * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
 * @param string $user_name 姓名
 * @return string 格式化后的姓名
 */
function half_truename($user_name){
    $strlen     = mb_strlen($user_name, 'utf-8'); //获取长度
    $firstStr   = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
    if($strlen == 2)
    {
        $true_name= "*".$lastStr;
    }
    else
    {
        $true_name= $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
    return $true_name;

}

//发送邮件
function send_mail($sendto,$subject,$body,$username='',$scene,$code,$time = 10){

    $email_host=config("email_host");//发送方的SMTP服务器地址
    $email_username=config("email_username");//申请163的SMTP服务使用的163邮箱
    $email_password=config("email_password");//客户端授权密码
    $from_name=config("email_from_name");//邮箱标题
    $to_name=$username;

    require(Env::get('root_path').'extend/sendmail/class.phpmailer.php');
    $mail = new \PHPMailer();
    $toemail = $sendto;//定义收件人的邮箱
    $mail->isSMTP();// 使用SMTP服务
    $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
    $mail->Host = $email_host;//"smtp.163.com";// 发送方的SMTP服务器地址
    $mail->SMTPAuth = true;// 是否使用身份验证
    $mail->Username = $email_username;// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱</span><span style="color:#333333;">
    $mail->Password = $email_password;//</span><span style="color:#ff6666;">// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！</span><span style="color:#333333;">
    $mail->SMTPSecure = "ssl";//</span><span style="color:#ff6666;">// 使用ssl协议方式</span><span style="color:#333333;">
    $mail->Port = 994;// 163邮箱的ssl协议方式端口号是465/994

    $mail->setFrom($email_username,$from_name);// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
    $mail->addAddress($toemail,$to_name);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
    $mail->addReplyTo($email_username,"Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址

    $mail->Subject = "注册验证邮件";// 邮件标题
    $mail->Body = $body;//"邮件内容是 <b>您的验证码是：123456</b>，哈哈哈！";// 邮件正文
    $send =  $mail->Send();

    $sms_log_data = [
        'scene' => $scene,
        'user_email' => $sendto,
        'status' => 1,
        'code' => $code,
        'msg' => $body,
        'add_time' => time(),
        'validity_time' => time() + $time * 60,
        'ip' => request()->ip(),
    ];
    if ($send) {
        //写入短信记录
        $rel = db('sms_email_log')->insert($sms_log_data);
        return return_array(1, lang('email_code_send_success'));
    } else {
        $sms_log_data['status'] = 2;
        $sms_log_data['error_msg'] = $send;
        $rel = db('sms_email_log')->insert($sms_log_data);
        return return_array(0, lang('email_code_send_fail'));
    }

}

function check_email($email)
{
    $result = trim($email);

    if (filter_var($result, FILTER_VALIDATE_EMAIL))
    {
        return "true";
    }
    else
    {
        return "false";
    }
}

if (!function_exists('return_array')) {
    /**
     * 格式化返回的数组
     * @param string $code 返回的状态吗
     * @param string $msg 提示信息
     * @param array $data 返回的数据信息
     * @author kcode <kcode@xxx.xxx>
     * @return array
     */
    function return_array($code = '', $msg = '', $data = null) {
        $retrun_data=[
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
        ];
        return $retrun_data;
    }

}

if (!function_exists('return_json')) {
    /**
     * 格式化返回的数组
     * @param string $code 返回的状态吗
     * @param string $msg 提示信息
     * @param array $data 返回的数据信息
     * @author kcode <kcode@xxx.xxx>
     * @return json
     */
    function return_json($code = '', $msg = '', $data = '') {
        $retrun_data=[
            'code'=>$code,
            'msg'=>$msg,
            'data'=>$data,
        ];
        return json($retrun_data);
    }

}

if (!function_exists('formatnum')) {
    //数字格式化
    function formatnum($number, $length = 2, $zero = '')
    {
        $array = array();
        $number = $number == '' ? 0 : $number;
        $array = explode(".", $number);
        if (!isset($array[1])) {
            $array[1] = '000000000';
        }
        $array[1] = substr($array[1], 0, $length);
        if ($array[1] == 0) {
            $number = $zero == '' ? $array[0] . ".00" : $array[0];
        } else {
            $number = $array[0] . "." . ($array[1] > 9 ? $array[1] : $array[1] . "0");
        }
        return $number;
    }
}

/**
 * php 替换字符串中间位置字符为星号
 * @param string $str
 * 字符串
 * @repeat 替换的字符
 * return string
 *
 */
if (!function_exists("half_replace")) {

    function half_replace($str) {
        $c = strlen($str) / 2;
        $string = preg_replace('|(?<=.{' . (ceil($c / 2)) . '})(.{' . floor($c) . '}).*?|', str_pad('', floor($c), '*'), $str, 1);
        return $string;
    }
}

/**
 * $string 明文或密文
 * $operation 加密ENCODE或解密DECODE
 * $key 密钥
 * $expiry 密钥有效期 ， 默认是一直有效
 * 摘自 discuz
 */
if (!function_exists("auth_code")) {

    function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        /*
         * 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
         * 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
         * 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
         * 当此值为 0 时，则不产生随机密钥
         */
        $ckey_length = 4;
        $key = md5($key != '' ? $key : "fdsfdf43535svxfsdfdsfs"); // 此处的key可以自己进行定义，写到配置文件也可以
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr(str_replace([
            '-',
            '_',
        ], [
            '+',
            '/',
        ], $string), $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = [];
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace([
                    '+',
                    '/',
                    '=',
                ], [
                    '-',
                    '_',
                    '',
                ], base64_encode($result));
        }
    }
}

if (!function_exists('change_user_money')) {
    /**
     * 改变用户的金钱记录
     * @param $user_id int 用户user_id
     * @param $money_type int 操作的金币类型
     * @param $action string 增加还是减少  默认＋　
     * @param $money string 金额　
     * @param $message string 　详细描述信息
     * @param string $operation_type 操作类型
     * @param $bonus_type string 　奖金类型
     * @param $parentid string 　 关联父ID
     * @param $payment_time string 确认时间　
     * @return int
     */
    function change_user_money($user_id, $money_type, $action, $money, $message, $operation_type = 0, $bonus_type = 0, $parentid = 0, $payment_time = ''){

        $user = Db::name('user_money')->where('user_id',$user_id)->find();

        $M_log = Db::name("user_money_log");
        $money = formatnum($money, 8);
        $id = 0;

        if (is_array($user) && $money > 0) {
            $money_field = UserMoneyType::get_user_money_type($money_type);
            $arr[$money_field] = $action == '+' ? $user[$money_field] + $money : $user[$money_field] - $money;
            $where = array();
            $where['user_id'] = $user['user_id'];

            if($arr[$money_field] < 0) {
                return 0;
            }
            $rel_money=Db::name('user_money')->where($where)->data($arr)->update(); // 根据条件保存修改的数据
            if(!$rel_money){
                return 0;
            }

            $log['user_id'] = $user['user_id'];
            $log['money_type'] = $money_type;
            $log['money'] = $action . $money;
            $log['money_action'] = $action;
            $log['balance'] = $arr[$money_field];
            $log['messages'] = $message;
            $log['add_time'] = $payment_time ? $payment_time : time();
            $log['parent_id'] = $parentid;
            $log['status'] = 1;
            $log['pay_time'] = $payment_time ? $payment_time : time();
            $log['operation_type'] = $operation_type;
            $log['bonus_type'] = $bonus_type;

            $rel = $M_log->insert($log);
            $id = $M_log->getLastInsID();
        }
        return $id;
    }

}
if (!function_exists('change_user_money_new')) {
    /**
     * 改变用户的金钱记录
     * @param int $user_id 用户user_id
     * @param int $money_type 操作的金币类型
     * @param string $action 增加还是减少  默认　＋　
     * @param string $money 金额　
     * @param string $message　详细描述信息
     * @param string $operation_type 操作类型
     * @param string $bonus_type　奖金类型
     * @param string $parentid　 关联父ID
     * @param string $payment_time 确认时间　
     * @return int
     */
    function change_user_money_new($user_id, $money_type, $action, $money, $message, $lang_val, $message_lang, $operation_type = 0, $bonus_type = 0, $parentid = 0, $payment_time = '')
    {

        $user = Db::name('user_money')->where('user_id',$user_id)->find();
        $M_log = Db::name("user_money_log");
        $money = formatnum($money, 8);
        $id = 0;

        if (is_array($user) && $money >= 0) {
            $money_field = UserMoneyType::get_user_money_type($money_type);
            $arr[$money_field] = $action == '+' ? $user[$money_field] + $money : $user[$money_field] - $money;
            $where = array();
            $where['user_id'] = $user['user_id'];

            if($arr[$money_field] < 0)
            {
                return 0;
            }
            $rel_money=Db::name('user_money')->where($where)->data($arr)->update(); // 根据条件保存修改的数据
            if(!$rel_money)
            {
                return 0;
            }

            $log['user_id'] = $user['user_id'];
            $log['money_type'] = $money_type;
            $log['money'] = $action . $money;
            $log['money_action'] = $action;
            $log['balance'] = $arr[$money_field];
            $log['messages'] = $message;
            $log['lang_val'] = $lang_val;
            $log['messages_lang'] = $message_lang;
            $log['add_time'] = $payment_time ? $payment_time : time();
            $log['parent_id'] = $parentid;
            $log['status'] = 1;
            $log['pay_time'] = $payment_time ? $payment_time : time();
            $log['operation_type'] = $operation_type;
            $log['bonus_type'] = $bonus_type;

            $rel = $M_log->insert($log);
            $id = $M_log->getLastInsID();
        }
        return $id;
    }

}

/**
 * 检查手机号码格式
 * @param string $mobile
 * @return boolean
 */
if (!function_exists("checkMobile")) {

    function checkMobile($mobile) {
        $search = "/^1[0-9]{10}$/i";
        if (preg_match($search, $mobile)) {
            return true;
        }
        return false;
    }
}

/**
 * 检查密码格式
 * @param string $passwd
 * @return boolean
 */
if (!function_exists("checkPasswd")) {

    function checkPasswd($passwd) {
        //  if (preg_match("/^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){5,16}$/", $passwd)) {

        if (preg_match("/^[a-zA-Z\d_]{6,18}$/", $passwd)) {
            return true;
        }
        return false;
    }
}

/**
 * 检查支付密码格式
 * 格式：数字字母下划线以及.*，长度6-18位
 * @param string $passwd
 * @return boolean
 */
if (!function_exists("checkPayword")) {

    function checkPayword($passwd) {
//        if(strlen($passwd)!=6)
//        {
//            return false;
//        }
        $pattern = "/^[\w.*]{6,18}$/";//格式：数字字母下划线以及.*，长度6-18位
//        if (preg_match("/^[0-9]{6,6}$/", $passwd)) {
        if (preg_match($pattern, $passwd)) {
            return true;
        }

        return false;
    }
}

/**
 * 检查用户名格式
 * @param string $passwd
 * @return boolean
 */
if (!function_exists("checkUsername")) {
    function checkUsername($username) {
        if (preg_match("/^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){5,16}$/", $username)) {
            return true;
        }
        return false;
    }
}

// PHP去除Html所有标签、空格以及空白
if (!function_exists("cutstr_html")) {
    function cutstr_html($str) {
        $str = trim($str); // 清除字符串两边的空格
        $str = strip_tags($str, ""); // 利用php自带的函数清除html格式
        $str = preg_replace("/\t/", "", $str); // 使用正则表达式替换内容，如：空格，换行，并将替换为空。
        $str = preg_replace("/\r\n/", "", $str);
        $str = preg_replace("/\r/", "", $str);
        $str = preg_replace("/\n/", "", $str);
        $str = preg_replace("/ /", "", $str);
        $str = preg_replace("/  /", "", $str); // 匹配html中的空格
        return trim($str); // 返回字符串
    }
}

/**
 * 发送短信
 * @param string $user_phone 用户手机号码
 * @param string $code 验证码
 * @param string $msg 发送的短信内容
 * @param int    $scene 发送的场景
 * @param int    $time 验证号有效分钟数
 * @author xxx <xxx@xx.com>
 * @return array
 */
if (!function_exists("spend_sms")) {
    function spend_sms($user_phone, $code, $msg, $scene = 0, $time = 10)
    {
        //验证当前场景是否可以可以发短信
        $check_spen_sms=check_spen_sms($user_phone, $code, $scene);
        if($check_spen_sms['code']==0)
        {
            return $check_spen_sms;
        }
        //只有正式环境才发送真实短信
        if (Config('app_phone')) {
            $smsapi = "http://api.smsbao.com/";
            $user = Config('SMS_USER'); //短信平台帐号
            $pass = md5(Config('SMS_PASSWORD')); //短信平台密码
            $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $user_phone . "&c=" . urlencode($msg);
            $result = file_get_contents($sendurl);
        } else {
            $result = 0;
        }
        $sms_log_data = [
            'scene' => $scene,
            'user_phone' => $user_phone,
            'status' => 1,
            'code' => $code,
            'msg' => $msg,
            'add_time' => time(),
            'validity_time' => time() + $time * 60,
            'ip' => request()->ip(),
        ];
        if ($result == 0) {
            $rel = db('sms_log')->insert($sms_log_data);
            //写入短信记录
            return return_array(1, lang("Sent_successfully"));
        } else {
            $sms_log_data['status'] = 2;
            $sms_log_data['error_msg'] = $result;
            $rel = db('sms_log')->insert($sms_log_data);
            return return_array(0, lang("Sent_failed"));
        }
    }
}

//检测当前场景是否可以发送短信
if (!function_exists("check_spen_sms")) {

    function check_spen_sms($user_phone, $code, $scene= 0)
    {
        //验证参数是否正确
        if(!checkMobile($user_phone))
        {
            return return_array(0, lang("mobile_format_error"));
        }


        //场景是否正确 1.注册 2.找回密码 3.登录 4 5 保留
        if(!in_array($scene, array(1,2,3,4,5,6)))
        {
            return return_array(0, lang("scene_not_defined"));
        }

        //查询短信发送的记录
        $sms_log=db('sms_log')
            ->where('scene', $scene)
            ->where('user_phone', $user_phone)
            ->order('id desc')
            ->find();
        //验证记录是否存在
        if(!$sms_log)
        {
            return return_array(1, "可以发送");
        }
        //验证验证码是否正确
        $check_time=time()-60;
        if($sms_log['add_time'] > $check_time)
        {
            return return_array(0, lang("message_has_sent"));
        }
        //检验验证码成功！
        return return_array(1, "可以发送！");
    }
}

/**
 * 检查验证码是否正确
 * @param string $user_phone 用户手机号码
 * @param string $code 验证码
 * @param int    $scenne 发送的场景
 * @author xxx <xxx@xx.com>
 * @return array
 */
if (!function_exists("check_sms_code")) {

    function check_sms_code($user_phone, $code, $scene)
    {
        //查询短信发送的记录
        $sms_log_info=Db::name('sms_log')
            ->where('scene', $scene)
            ->where('user_phone', $user_phone)
            ->order('id desc')
            ->find();
        //验证记录是否存在
        if(! $sms_log_info)
        {
            return return_array(-1, lang('CAPTCHA_invalid'));
        }
        //验证验证码是否正确
        if($sms_log_info['code'] != $code)
        {
            return return_array(-2, lang('CAPTCHA_invalid'));
        }

        //查看是否在有效期内
        if($sms_log_info['validity_time'] <time())
        {
            return return_array(-3, lang('CAPTCHA_invalid'));
        }
        //检验验证码成功！
        return return_array(1, lang('CAPTCHA_verification_success'));
    }
}

/**
 * php替换富文本框返回图片路径
 * @param string $content 富文本文本内容
 * @author xxx <xxx@xx.com>
 * @return string
 */
if (!function_exists("replace_image_url")) {
    function replace_image_url($content) {
        $url = "http://".$_SERVER['SERVER_NAME'];
        $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
        $content = preg_replace($pregRule, '<img src="'.$url.'${1}" style="max-width:100%">', $content);
        return $content;
    }
}

/**
 * 给用户发送信
 * @param string $content 富文本文本内容
 * @author xxx <xxx@xx.com>
 * @return string
 */
if (!function_exists("spend_user_message")) {
    function spend_user_message($user_id,$title,$content,$spend_uid=0) {
        $spned_message=User::spendUserMessage($spend_uid,$user_id,$title,$content);
        return $spned_message;
    }
}

if (!function_exists('createRandomStr')) {
    function createRandomStr($length, $str = '')
    {
        if ($str == '') {
            $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 62个字符
        }
        $strlen = 62;
        while ($length > $strlen) {
            $str .= $str;
            $strlen += 62;
        }
        $str = str_shuffle($str);
        return substr($str, 0, $length);
    }
}

if (!function_exists("check_sms_code_email")) {
    function check_sms_code_email($user_email, $code, $scene){

        //查询短信发送的记录
        $sms_log_info=Db::name('sms_email_log')
            ->where('scene', $scene)
            ->where('user_email', $user_email)
            ->order('id desc')
            ->find();
        //验证记录是否存在
        if(! $sms_log_info)
        {
            return return_array(-1, lang('CAPTCHA_invalid'));
        }
        //验证验证码是否正确
        if($sms_log_info['code'] != $code)
        {
            return return_array(-2, lang('CAPTCHA_invalid'));
        }

        //查看是否在有效期内
        if($sms_log_info['validity_time'] <time())
        {
            return return_array(-3, lang('CAPTCHA_invalid'));
        }
        //检验验证码成功！
        return return_array(1, lang('CAPTCHA_verification_success'));
    }
}

/**
 * 日志
 */
if(!function_exists('log_to_data')){
    function log_to_data($name, $title, $content)
    {
        $ym = date('Ym');

        $path = ROOT_PATH.'runtime/'. $name;
        $path1 = $path . '/' . $ym;
        if (!is_dir($path1)) {
            mkdir($path1. '/', 0777, true);
        }
        if(is_array($content)) {
            $content = var_export($content, true);
        }

        chgrp($path, 'www');
        chgrp($path1, 'www');
        chown($path,"www");
        chown($path1,"www");
        $filename = $path1 . '/' . date('d'). '.log';
        file_put_contents($filename, $title.':['.date('Y-m-d H:i:s').']:'.$content . PHP_EOL, FILE_APPEND);
        chgrp($filename,'www');
        chown($filename,"www");
        chmod($filename, 0755);
    }
}

// 将科学计数法的数字转换为正常的数字
if(!function_exists('convert_scientific_number_to_normal')){
    /**
     * 将科学计数法的数字转换为正常的数字
     * 为了将数字处理完美一些，使用部分正则是可以接受的
     * @author loveyu
     * @param string $number
     * @return string
     */
    function convert_scientific_number_to_normal($number)
    {
        if(stripos($number, 'e') === false) {
            //判断是否为科学计数法
            return $number;
        }

        if(!preg_match(
            "/^([\\d.]+)[eE]([\\d\\-\\+]+)$/",
            str_replace(array(" ", ","), "", trim($number)), $matches)
        ) {
            //提取科学计数法中有效的数据，无法处理则直接返回
            return $number;
        }

        //对数字前后的0和点进行处理，防止数据干扰，实际上正确的科学计数法没有这个问题
        $data = preg_replace(array("/^[0]+/"), "", rtrim($matches[1], "0."));
        $length = (int)$matches[2];
        if($data[0] == ".") {
            //由于最前面的0可能被替换掉了，这里是小数要将0补齐
            $data = "0{$data}";
        }

        //这里有一种特殊可能，无需处理
        if($length == 0) {
            return $data;
        }

        //记住当前小数点的位置，用于判断左右移动
        $dot_position = strpos($data, ".");
        if($dot_position === false) {
            $dot_position = strlen($data);
        }

        //正式数据处理中，是不需要点号的，最后输出时会添加上去
        $data = str_replace(".", "", $data);


        if($length > 0) {
            //如果科学计数长度大于0

            //获取要添加0的个数，并在数据后面补充
            $repeat_length = $length - (strlen($data) - $dot_position);
            if($repeat_length > 0) {
                $data .= str_repeat('0', $repeat_length);
            }

            //小数点向后移n位
            $dot_position += $length;
            $data = ltrim(substr($data, 0, $dot_position), "0").".".substr($data, $dot_position);

        } elseif($length < 0) {
            //当前是一个负数

            //获取要重复的0的个数
            $repeat_length = abs($length) - $dot_position;
            if($repeat_length > 0) {
                //这里的值可能是小于0的数，由于小数点过长
                $data = str_repeat('0', $repeat_length).$data;
            }

            $dot_position += $length;//此处length为负数，直接操作
            if($dot_position < 1) {
                //补充数据处理，如果当前位置小于0则表示无需处理，直接补小数点即可
                $data = ".{$data}";
            } else {
                $data = substr($data, 0, $dot_position).".".substr($data, $dot_position);
            }
        }
        if($data[0] == ".") {
            //数据补0
            $data = "0{$data}";
        }
        return trim($data, ".");
    }
}
/**
 * post 请求
 */
if(!function_exists('send_post')){
    function send_post()
    {
        if(!Request::isPost()) {
            exit (json_encode(return_array(0,lang("request_error"))));
        }
    }
}

/**
 * 天时分秒倒计时
 * @param $beginDate  开始日期
 * @param $endDate    结束日期
 * @return string
 */
function timeCalculation($beginDate, $endDate)
{
    $subTime = strtotime($endDate) - strtotime($beginDate);
    $day = $subTime > 86400 ? floor($subTime / 86400) : 0;
    $subTime -= $day * 86400;
    $hour = $subTime > 3600 ? floor($subTime / 3600) : 0;
    $subTime -= $hour * 3600;
    $minute = $subTime > 60 ? floor($subTime / 60) : 0;
    $subTime -= $minute * 60;
    $second = $subTime;
    $dayText = $day ? $day . '天' : '';
    $hourText = $hour ? $hour . '小时' : '';
    $minuteText = $minute ? $minute . '天' : '';
    $date = $dayText . $hourText . $minuteText . $second . '秒';
    return $date;
}
/**
 * 处理科学计数法
 * @param $num       科学计数法字符串  如 2.1E-5
 * @param int $double 小数点保留位数 默认8位
 * @return string
 */
if(!function_exists('sctonum')) {
    function sctonum($num, $double = 8){
        $num = strtolower(trim($num));
    
        if(false !== stripos($num, "e")){
            $a = explode("e",strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1], $double), $double);
        }
        return $num;
    }
}

/**
 * 处理小数为0
 */
if(!function_exists('trim_number')){
    function trim_number($num)
    {
        if(preg_match("/^[1-9][0-9]*$/",$num)){
            return (string)$num;
        }
        if (strpos($num, '.') !== false) {
            return rtrim(rtrim($num, 0),'.');
        }
        return floatval($num);
    }
}






