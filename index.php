<?php
#error_reporting(-1);

ob_start();
include('val.php');

$my_bot = [
    [['text' => $name_bot, 'url' => $url_bot]],
];

define('API_KEY', $API_KEY);
function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    #file_put_contents('error.txt', $res."\n$method".__LINE__, FILE_APPEND);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
        $res = json_decode($res);
        return $res;
    } else {
        $res = json_decode($res);
        return $res;
    }
}
function shortNumber($num) 
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}
 
function rand_text(){
    $abc = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
    $fol = '#'.$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)];
    return $fol;
}


function check_m($id, $chat){
    $join = bot('getChatMember', ["chat_id" => $chat, "user_id" => $id])->result->status;
    if($join == 'left' or $join == 'kicked'){
        return false;
    }else{
        return true;
    }
}

$up = file_get_contents('php://input');
$update = json_decode($up);
if ($update->message) {
    $message = $update->message;
    $chat_id = $message->chat->id;
    $text = $message->text;
    $extext = explode(" ", $text);
    $first_name = $update->message->from->first_name;
    $username = $message->from->username;
    $id = $message->from->id;
    $message_id = $message->message_id;
    $entities = $message->entities;
    $language_code = $message->from->language_code;
    $tc = $update->message->chat->type;
    $jsons = json_decode(file_get_contents('data/data.json'), true);
    $get_jsons = json_decode(file_get_contents('data/data.json'));
    $re_message = $update->message->reply_to_message;
    $re_text = $re_message->text;
}


//data callback
if ($update->callback_query) {
    $chat_id2 = $update->callback_query->message->chat->id;
    $id2 = $update->callback_query->from->id;
    $first_name = $update->callback_query->from->first_name;
    $message_id2 = $update->callback_query->message->message_id;
    $data = $update->callback_query->data;
    $exdata = explode("|", $data);
    $jsons = json_decode(file_get_contents('data/data.json'), true);
    $get_jsons = json_decode(file_get_contents('data/data.json'));
}


if($update->inline_query->query){
    $inline = $update->inline_query;
    $query_id = $inline->id;
    $query = $inline->query;
    $query_form_id = $inline->from->id;
    if($query == 'mylink'){
        bot('answerInlineQuery',[
            'inline_query_id'=>$query_id,    
            'cache_time'=>0,
            'results' => json_encode([[
                'type'=>'article',
                'id'=>base64_encode(rand(5,555)),
                'title'=>"إضغط هنا لنشر رابط الدعوة الخاص بك",
                'description'=>"✅ البوت الاول لرشق المتابعين + مشاهدات + لايكات + مشاركات تيليجرام - انستقرام - يوتيوب - فيسبوك - تيك توك  - تويتر - سناب شات - لايكي - تلقائياً مجاناً. ♻️👇",
                'disable_web_page_preview'=>'true',
                'input_message_content'=>['disable_web_page_preview'=>true,'message_text'=>"✅ البوت الاول لرشق المتابعين + مشاهدات + لايكات + مشاركات تيليجرام - انستقرام - يوتيوب - فيسبوك - تيك توك  - تويتر - سناب شات - لايكي - تلقائياً مجاناً. ♻️👇"],
                    'reply_markup' => ['inline_keyboard' => [ 
                        [['text' => "إضغط هنا للدخول إلى البوت", 'url' => $link_invite.$query_form_id]],
                        ]
                    ]
            ]])
        ]);
    }
}
$bans = explode("\n", file_get_contents("data/ban.txt"));
$is_ok = file_get_contents('data/is_ok.txt');
$is_no = file_get_contents('data/is_no.txt');
$ex_is_ok = explode("\n", $is_ok);
$ex_is_no = explode("\n", $is_no);

if($message){
    if(!in_array($id, $adminss)){
        if (in_array($id, $ex_is_no) or in_array($id, $bans)) {
            bot('sendmessage', [
                'chat_id' => $id,
                'text' => "لا يمكنك استخدام البوت",
                'reply_to_message_id' => $message_id
            ]);
            return;
        } 
    }
}

$json_config = json_decode(file_get_contents('data/config.json'), true);
$config = json_decode(file_get_contents('data/config.json'));
$run = $config->run;

$members = file_get_contents('data/members.txt');
$exmembers = explode("\n", $members);
if (!in_array($id, $exmembers) and $update->message){
    $jsonsstart = json_decode(file_get_contents('data/cache.json'), true);
    $get_jsonsstart = json_decode(file_get_contents('data/cache.json'));
    if(in_array($extext[1], $exmembers)){
        if($extext[0] == '/start' && $extext[1] != null){
            $jsonsstart["$id"] = $extext[1];
            file_put_contents("data/cache.json", json_encode($jsonsstart));
            $IS_LINK = true;
        }
    
    }
    $ch_sub = $config->channel;
    $join = bot('getChatMember', ["chat_id" => $ch_sub, "user_id" => $id])->result->status;
    if($config->runchannel != 'stop'){
        if ($join == 'left' or $join == 'kicked') {
            bot('sendMessage',[
                    'chat_id' => $chat_id,
                    'text' => "
عذرا.. ⚠️
لايمكنك استخدام البوت حتى تشترك في القنوات..

$ch_sub

اشترك في القناة ثم أرسل 
/start
        ",

                ]
            );
            return;
        }
    }
    $get_s = $get_jsonsstart->{$id};
    if($get_s != null or $IS_LINK){
        if (!$message->contact->user_id && !in_array($id, $ex_is_ok) && !in_array($id, $ex_is_no)) {
            bot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "يجب التأكد من هويتك، قم بالضغط علئ زر التحقق من الحساب التحقق من الحسابات الوهميه وهذا لا ياثر علئ حسابك اطلاقآ",
                'reply_to_message_id' => $message_id,
                "reply_markup" => json_encode([
                    "keyboard" => [
                        [["text" => "التحقق من الحساب", "request_contact" => true]],
                    ]
                ])
            ]);
            return;
        }
        
        if (!in_array($id, $ex_is_ok) && !in_array($id, $ex_is_no)) {
            if ($message->contact->user_id == $id) {
                $number = "+".$message->contact->phone_number;
                foreach ($ban_num as $one) {
                    if (preg_match("/(".$one.")/", $number, $mach)) {
                        $is_ban = true;
                        break;
                    } else {
                        $is_ban = false;
                    }
                }

                if ($is_ban) {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "جهة الاتصال وهمية ، تم حظرك من البوت",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => json_encode([
                            'remove_keyboard' => true
                        ])
                    ]);
                    file_put_contents('data/is_no.txt', $id."\n", FILE_APPEND);
                    return;
                } else {
                    bot('sendmessage', [
                        'chat_id' => $chat_id,
                        'text' => "تم تأكيد جهة الاتصال الخاصة بك ،يمكنك استخدام البوت الآن ارسل /start",
                        'reply_to_message_id' => $message_id,
                        'reply_markup' => json_encode([
                            'remove_keyboard' => true
                        ])
                    ]);
                    file_put_contents('data/is_ok.txt', $id."\n", FILE_APPEND);
                    include_once('./sql_class.php');
                    if (mysqli_connect_errno()) {
                        return;
                    }
                    $jsonsstart["$id"] = null;
                    file_put_contents("data/cache.json", json_encode($jsonsstart));
                    $us = $sql->sql_select('users', 'user', $get_s);
                    $coin = $us['coin'];
                    $invite = $config->invite;
                    $return = $coin + $invite;
                    $us = $sql->sql_edit('users', 'coin', $return, 'user', $get_s);
                    bot('sendmessage', [
                        'chat_id' => $get_s,
                        'text' => "
    دخل شخص عن طريق رابط الدعوة الخاص بك..

    تم إضافة $invite$ إلى رصيدك
                        ",
                    ]);
                
                #return;
                }
            } else {
                bot('sendmessage', [
                    'chat_id' => $chat_id,
                    'text' => "جهة الاتصال ليست تابعة لك..",
                    'reply_to_message_id' => $message_id
                ]);
                return;
            }
        }
    }
}

if ($message->text && !in_array($id, $exmembers)) {
    file_put_contents('data/members.txt', $id . "\n", FILE_APPEND);
    include_once("./sql_class.php");
    $all = count($exmembers);
    #$sql = new mysql_api_code($db);
    if($get_s == null){
        $get_s = 'None';
    }
    $v = $sql->sql_write('users(coin,user,spent,charge,mycoin,fromuser,coinfromuser)', "VALUES('0','$id','0','0','usd','$get_s','0')");
    bot('sendMessage', [
        'chat_id' => $dev,
        'text' => "
        *تم دخول شخص جديد الى البوت الخاص بك*
-----------------------
• معلومات العضو الجديد 
• الاسم : $first_name
• الايدي : $id
-----------------------
• عدد الاعضاء الكلي  : *$all*
 ",
        'parse_mode' => "MarkDown",
    ]);
}


if($message->text){
    $ch_sub = $config->channel;
    $join = bot('getChatMember', ["chat_id" => $ch_sub, "user_id" => $id])->result->status;
    if($config->runchannel != 'stop'){
        if ($join == 'left' or $join == 'kicked') {
            bot('sendMessage',[
                    'chat_id' => $chat_id,
                    'text' => "
    عذرا.. ⚠️
    لايمكنك استخدام البوت حتى تشترك في القنوات..

    $ch_sub

    اشترك في القناة ثم أرسل 
    /start
        ",

                ]
            );
        return;
        }
    }
}

function get_serv($file, $serv){
    require_once('apifiles/'.$file.".php");
    if($file == '1'){
        $api = new Api();
    }elseif($file == '2'){
        $api = new Api2();
    }elseif($file == '3'){
        $api = new Api3();
    }
    $services = $api->services();
    foreach($services as $s){
        $ss = json_decode(json_encode($s));
        if ($ss->service == $serv){
            $api = '';
            return [
                'rate' => $ss->rate,
                'min' => $ss->min,
                'max' => $ss->max
            ];
        }
    }
    $api = '';
    return false;
}


function get_vip($charge){
    if($charge < 100){
        return 0;
    }
    if($charge >= 500){
        $vip = 5;
    }elseif($charge >= 400){
        $vip = 4;
    }elseif($charge >= 300){
        $vip = 3;
    }elseif($charge >= 200){
        $vip = 2;
    }elseif($charge >= 100){
        $vip = 1;
    }
    return $vip;
}

function is_multi_ten($num){
    if($num <= 1){
        return false;
    }
    if($num % 10 == 0)  {
        return true;
    }else{
        return false;
    }
}
function isint($num){
    if ($num < 0){
        return false;
    }
    if(is_numeric($num)){
        return true;
    }else{
        return false;
    }
}

function get_coin_info($c){
    if($c == 'usd'){
        return [1,'دولار'];
    }
    if($c == 'y'){
        return [600,'ريال يمني قديم'];
    }
    if($c == 's'){
        return [4,'ريال سعودي'];
    }
    if($c == 'd'){
        return [1.7,'اسيا'];
    }
    if($c == 'j'){
        return [21,'جنيه مصري'];
    }
    if($c == 'r'){
        return [4,'درهم ٳماراتي'];
    }
    if($c == 'g'){
        return [4,'ريال قطري'];
    }
    if($c == 'o'){
        return [1200,'ريال يمني جديد'];
    }
}


$admin_button = [
    [['text' => "تعيين الستارت", 'callback_data' => "addstart"], ['text' => "تعيين نقاط الدخول", 'callback_data' => "addinvite"]],
    [['text' => "إضافة قسم رئيسي", 'callback_data' => "addcoll"],['text' => "حذف قسم رئيسي", 'callback_data' => "delcoll"]],
    [['text' => "إضافة قسم", 'callback_data' => "adddivi"],['text' => "حذف قسم", 'callback_data' => "deldivi"]],
    [['text' => "إضافة خدمة", 'callback_data' => "addserv"],['text' => "حذف خدمة", 'callback_data' => "delserv"]],
    [['text' => "إضافة رصيد", 'callback_data' => "addbalance"],['text' => "حذف رصيد", 'callback_data' => "delbalance"]],
    [['text' => "نسبة تحويل النقاط", 'callback_data' => "sel"],['text' => "أدنى حد للتحويل", 'callback_data' => "selmin"]],
    [['text' => "تعيين قناة الإشتراك", 'callback_data' => "addsub"],['text' => "تعيين الدليل", 'callback_data' => "addhelp"]],
];
$back = [
    [['text' => "إلغاء ورجوع", 'callback_data' => "back"]],
];
$back2 = [
    [['text' => "إلغاء ورجوع", 'callback_data' => "back2"]],
];
$back_add = [
    [['text' => "إلغاء ورجوع", 'callback_data' => "addusers"]],
];

$start = [
    [['text' => "بدا عملية الرشق ✅", 'callback_data' => "addusers"]],
    [['text' => "شراء نقاط 💸", 'url' => $add_balance],['text' => "تحويل نقاط ♻️", 'callback_data' => "sendmoney"]],
    [['text' => "معلومات حسابي 💲", 'callback_data' => "my"],['text' => "تجميع نقاط 💸", 'switch_inline_query' => "mylink"]],
    [['text' => "الاحصائيات 📊", 'callback_data' => "myaccount"],['text' => "دليل الاستخدام 🗂️", 'callback_data' => "help"]],
    [['text' => "تغير عملة البوت 🇾🇪🇸🇦🇪🇬🇺🇸🇮🇶", 'callback_data' => "changecoin"]],
 [['text' => "قناة البوت 📮", 'url' => $ch_bot],['text' => "قناة الإثبات 📮", 'url' => $channel]]
];
$changecoin = [
    [['text' => "ريال يمني قديمYEM 🇾🇪", 'callback_data' => "selectcoin|y"]],
    [['text' => "ريال يمني جديدYEM 🇾🇪", 'callback_data' => "selectcoin|o"]],
    [['text' => "ريال سعودي SAR 🇸🇦", 'callback_data' => "selectcoin|s"]],
    [['text' => "ريال قطري QAR 🇶🇦", 'callback_data' => "selectcoin|g"]],
    [['text' => "درهم إماراتي AED 🇦🇪", 'callback_data' => "selectcoin|r"]],
    [['text' => "اسيا IQD 🇮🇶", 'callback_data' => "selectcoin|d"]],
    [['text' => "جنيه مصري EGP 🇪🇬", 'callback_data' => "selectcoin|j"]],
    [['text' => "دولار أمريكي USD 🇺🇸", 'callback_data' => "selectcoin|usd"]],
    [['text' => "إلغاء ورجوع", 'callback_data' => "back2"]],
];

$ok = [
    [['text' => "الغاء ❌", 'callback_data' => "addusers"], ['text' => "تاكيد ✅", 'callback_data' => "done"]],
];

if ($update->message) {
    if($run == 'stop' && !in_array($id, $adminss)){
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => '*البوت قيد التحديث الرجاء الانتظار حتى يتم الانتهاء من التحديث سيتم اشعاركم بذالك فور الانتهاء*',
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
        ]);
        return;
    }
    if ($text == '/start') {
        include('./sql_class.php');
        $sq = $sql->sql_select('users', 'user', $id);
        $coin = $sq['coin'];
        $mycoin = $sq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_after_coin = $info_coin[0] * $coin;
        $coin_name = $info_coin[1];
        $user_one_dollar = explode("\n", file_get_contents('data/user_one_dollar.txt'));
        if(!in_array($id, $user_one_dollar)){
            if($coin >= '1'){
                file_put_contents('data/user_one_dollar.txt', $id."\n", FILE_APPEND);
            }
        }
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "
🙋🏻‍♂️  مرحبآ عزيزي : $first_name
ـــــــــــــــــــــــــــــــــــــــــــــ
↤ بوت خدمات الراقي للرشق
↤ هو البوت الأكثر تميزاً 
↤ لمساعدتك في رفع متابعينك        
↤ في جميع مواقع التواصل الاجتماعي
↤ متابعين + لايكات + مشاهدات
↤ بكل سهولة وامان

💵 رصيدك : $coin_after_coin $coin_name
ــــــــــــــــــــــــــــــــــــــــــــــ
            ",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $start
            ])
        ]);
        return;
    }

    if($text && $get_jsons->{$id}->data == 'sendmoney'){
        if(!in_array($text, $exmembers)){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "العضو غير مووجود في قائمة الأعضااء",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back2
                ])
            ]);
            return;
        }
        $jsons["$id"]["data"] = 'sendmoney2';
        $jsons["$id"]["for"] = $text;
        file_put_contents("data/data.json", json_encode($jsons));
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "أرسل القيمة المراد تحويلها",
            'reply_markup' => json_encode([
                'inline_keyboard' => $back2
            ])
        ]);
    }
    if($text && $get_jsons->{$id}->data == 'sendmoney2'){
        if(isint($text)){
            $min = $config->selmin;
            $prec = $config->sel;
            if($text < $min){
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "
يجب أن تكون قيمة التحويل أكثر من الحد الأدنى المسموح فيه للتحويل

الحد الأدنى : $min$
العمولة : $prec%
                    ",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back2
                    ])
                ]);
                return;
            }
            include('./sql_class.php');
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"حدث خطأ",
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            $us = $sql->sql_select('users', 'user', $id);
            $coin = $us['coin'];
            if($text > $coin){
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "رصيدك لا يكفي لهذه القيمة",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back2
                    ])
                ]);
                return;
            }
            $jsons["$id"] = null;
            file_put_contents("data/data.json", json_encode($jsons));
            $return = $coin - $text;
            $sql->sql_edit('users', 'coin', $return, 'user', $id);
            $for = $get_jsons->{$id}->for;
            $us_to = $sql->sql_select('users', 'user', $for);
            $coin_to = $us_to['coin'];
            $precent = ($text / 100) * $prec;
            $after_precent = $text - $precent;
            $return_to = $coin_to + $after_precent;
            $sql->sql_edit('users', 'coin', $return_to, 'user', $for);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
تم تحويل النقاط بنجاح..
ــــــــــــــــــــــــــــــــــــــــــــــ
من : $first_name
من : $id

إلى : $for

القيمة الكلية : $text$
العمولة : $precent$
القيمة بعد خصم العمولة : $after_precent$
تم تحويل : $after_precent$
رصيدك الآن : $return$
                ",
            ]);
            bot('sendMessage', [
                'chat_id' => $for,
                'text' => "
تحويل نقاط جديد إلى حسابك.
ــــــــــــــــــــــــــــــــــــــــــــــ
من : $first_name
من : $id

القيمة الكلية : $text$
العمولة : $precent$
القيمة بعد خصم العمولة : $after_precent$
تمت إلى رصيدك : $after_precent$
رصيدك الآن : $return_to$
                ",
            ]);
            foreach($adminss as $one){
                bot('sendMessage', [
                    'chat_id' => $one,
                    'text' => "
#عملية_تحويل

تم تحويل النقاط بنجاح..

من : $first_name
من : $id

إلى : $for

القيمة الكلية : $text$
العمولة : $precent$
القيمة بعد خصم العمولة : $after_precent$
تم تحويل : $after_precent$

رصيد المرسل قبل التحويل : $coin$
رصيد المرسل بعد التحويل : $return$

رصيد المستلم قبل التحويل : $coin_to$
رصيد المستلم بعد التحويل : $return_to$
                    ",
                ]);
            }
        }else{
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "يرجى إرسال القيمة كأرقام صحيحة",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back2
                ])
            ]);
            return;
        }
    }


    if($text && $get_jsons->{$id}->data == 'link'){
        $is_u = substr($text, 0, 1);
        $is_user = false;
        if($is_u[0] == '@'){
            $is_user = true;
        }
        if (filter_var($text, FILTER_VALIDATE_URL) === FALSE and !$is_user) {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "الرابط غير صالح",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        include('./sql_class.php');
        $but = $sql->sql_select('order_waiting', 'link', $text);
        if($but['link'] == 'link'){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
                لا يمكن رشق هذا الرابط أكثر من مرة في وقت واحد ، حاول مجددا بعد قليل أو أرسل رابط آخر
                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        $jsons["$id"]["data"] = 'num';
        $jsons["$id"]["link"] = $text;
        file_put_contents("data/data.json", json_encode($jsons));
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "
⇜ ارسل العدد المطلوب للرشق 
            ",
            'reply_markup' => json_encode([
                'inline_keyboard' => $back_add
            ])
        ]);
    }
    if($text && $get_jsons->{$id}->data == 'num'){
        if(!isint($text)){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
يرجى إرسال أرقاما صحيحة فقط
                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        if(!is_multi_ten($text)){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
يجب أن يكون العدد من مضاعفات الرقم 10

مثال 100, 1200, 110
                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        include('./sql_class.php');
        $sq = $sql->sql_select('users', 'user', $id);
        $coin = $sq['coin'];
        $serv = $get_jsons->{$id}->serv;
        $codeserv = $get_jsons->{$id}->codeserv;
        $sq22 = $sql->sql_select('serv', 'codeserv', $codeserv);
        $api = $sq22['api'];
        $name = $sq22['name'];
        $num = $sq22['num'];
        $prec = $sq22['precent'];
        $g = get_serv($api, $serv);
        if (!$g){
            $jsons["$id"] = null;
            file_put_contents("data/data.json", json_encode($jsons));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "الخدمة غير متاحة، تم إرسال التقرير إلى الإدارة",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            foreach($adminss as $one){
                bot('sendMessage', [
                    'chat_id' => $one,
                    'text' => "
#إبلاغ

حصل خطأ في أحد الخدمات
الخدمة : $name
رقم الخدمة : $num
ال api : $api
                    ",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back_add
                    ])
                ]);
            }
            return;
        }

        $sqsq = $sql->sql_select('users', 'user', $id);
        $mycoin = $sqsq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_name = $info_coin[1];

        $rate = $g['rate'];
        $price = (($rate / 100) * $prec) + $rate; //price of 1000
        $price2 = ((($rate / 100) * $prec) + $rate) * $info_coin[0]; //price of 1000
        $price_one = $price / 1000;
        $price_order = $price_one * $text;
        $price_order2 = ($price_one * $text) * $info_coin[0];
        $coin2 = $coin * $info_coin[0];
        $coin_after = $coin - $price_order;
        $coin_after2 = ($coin - $price_order) * $info_coin[0];
        $min = $g['min'];
        $max = $g['max'];
        if ($text < $min or $text > $max){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
الخدمة : $name
سعر الف عضو : $price2 $coin_name

أدنى حد للرشق هو $min وأقصى حد هو $max

أرسل العدد المطلوب من جديد ويجب أن يكون محصورا بين الحد الأدنى والأقصى
                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        if($coin < $price_order){
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
⇜ رصيدك غير كافي لهذا العدد
                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back_add
                ])
            ]);
            return;
        }
        $jsons["$id"]["data"] = 'done';
        $jsons["$id"]["num"] = $text;
        $jsons["$id"]["api"] = $api;
        $jsons["$id"]["price_order"] = $price_order;
        $jsons["$id"]["price_k"] = $price;
        file_put_contents("data/data.json", json_encode($jsons));
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "
الخدمة : $name
كود الخدمة : $codeserv
السعر لكل الف عضو : $price2 $coin_name
العدد المطلوب : $text
السعر الكلي : $price_order2 $coin_name
رصيدك الحالي : $coin2 $coin_name

سيتبقى رصيدك في حال الطلب : $coin_after2 $coin_name

هل تريد تأكيد عملية الرشق؟
            ",
            'reply_markup' => json_encode([
                'inline_keyboard' => $ok
            ])
        ]);
        return;
    }

    /*  
    * أوامر الأدمن
    */
    if (in_array($id, $adminss)) {
        $json = json_decode(file_get_contents('data/admin.json'), true);
        $get_json = json_decode(file_get_contents('data/admin.json'));
        if ($text == '/admin') {
            #$members = explode("\n", file_get_contents('data/members.txt'));
            #$countuser = count($members) - 1;
            require_once('apifiles/1.php');
            require_once('apifiles/2.php');
            require_once('apifiles/3.php');
            $api = new Api();
            $balance = json_decode(json_encode($api->balance()))->balance;
            $api1 = new Api2();
            $balance1 = json_decode(json_encode($api1->balance()))->balance;
            $api2 = new Api3();
            $balance2 = json_decode(json_encode($api2->balance()))->balance;
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
 اهلا عزيزي المطورفي لوحة التحكم الخاصة 

الرصيد :
api 1 : $balance$
api 2 : $balance1$
api 3 : $balance2$

تشغيل البوت : /run
تعطيل البوت : /stop

تشغيل الاشتراك : /runchannel
تعطيل الاشتراك : /stopchannel

لحظر عضو :
/ban id
لإلغاء عضو :
/unban id

جلب معلومات عضو :
/get_user id

جلب معلومات خدمة :
/get_serv #id

                ",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $admin_button
                ])
            ]);
            return;
        }
        if($extext[0] == '/pro'){
            $del = str_replace($extext[1], '', $is_no);
            file_put_contents('data/is_no.txt', $del);
            file_put_contents('data/is_ok.txt', $extext[1]."\n", FILE_APPEND);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم",
            ]);
            bot('sendMessage', [
                'chat_id' => $extext[1],
                'text' => "تم التحقق من حسابك بنجاح.. أرسل /start للمواصلة",
            ]);
            return;  
        }
        if($extext[0] == '/get_user'){
            include('./sql_class.php');
            $us = $sql->sql_select('users', 'user', $extext[1]);
            #coin,user,spent,charge
            $coin = $us['coin'];
            $charge = $us['charge'];
            $spent = $us['spent'];
            $fromuser = $us['fromuser'];
            $coinfromuser = $us['coinfromuser'];
            $vip = get_vip($charge);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
معلومات المستخدم :
ــــــــــــــــــــــــــــــــــــــــــــــ
رصيده الحالي : $coin$
رصيده المصروف : $spent$
الرصيد المشحون : $charge$
مستوى الحساب : VIP$vip

رصيده من دعوة الاعضاء الكلي : $coinfromuser$
تمت دعوته إلى البوت من قبل : $fromuser$
                ",
            ]);
            return;  
        }
        if($extext[0] == '/get_serv'){
            include('./sql_class.php');
            $us = $sql->sql_select('serv', 'codeserv', $extext[1]);
            $name = $us['name'];
            $code = $us['code'];
            $cap = $us['caption'];
            $num = $us['num'];
            $api = $us['api'];
            $prec = $us['precent'];
            $serv_but = $sql->sql_select('buttons', 'code', $code);
            $name_but = $serv_but['name'];
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
معلومات الخدمة :
 
اسم الخدمة : $name
تابعة للقسم : $name_but
وصف الخدمة : $cap
رقم الخدمة : $num
ال api : $api
نسبة الربح : $prec%
                ",
            ]);
            return;  
        }
        if($extext[0] == '/ban'){
            file_put_contents("data/ban.txt", $extext[1]."\n", FILE_APPEND);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم حظره",
            ]);
            bot('sendMessage', [
                'chat_id' => $extext[1],
                'text' => "تم حظرك من البوت",
            ]);
            return;  
        }
        if($extext[0] == '/unban'){
            $f = file_get_contents("data/ban.txt");
            $f = str_repeat($extext[1], '', $f);
            file_put_contents("data/ban.txt", $f);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم إلغاء حظره",
            ]);
            bot('sendMessage', [
                'chat_id' => $extext[1],
                'text' => "تم إلغاء حظرك من البوت",
            ]);
            return;
        }
        if($text && $get_json->data == 'addsub'){
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            $json_config["channel"] = $text;
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تعيين قناة الاشنراك",
            ]);
            return;
        }
        if($text == '/runchannel'){
            $json_config["runchannel"] = 'run';
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تشغيل الاشتراك",
            ]);
            return;
        }
        if($text == '/stopchannel'){
            $json_config["runchannel"] = 'stop';
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تعطيل الاشتراك",
            ]);
            return;
        }
        if($text == '/run'){
            $json_config["run"] = 'run';
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تشغيل البوت",
            ]);
            return;
        }
        if($text == '/stop'){
            $json_config["run"] = 'stop';
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تعطيل البوت",
            ]);
            return;
        }
        /*
        * start
        */
        if ($text and $get_json->data == 'addstart') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            $json_config["start"] = $text;
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تعيين start",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * نقاط الدخول
        */
        if ($text and $get_json->data == 'addinvite') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            if(isint($text)){
                $json_config["invite"] = $text;
                file_put_contents("data/config.json", json_encode($json_config));
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "تم تعيين start",
                    'parse_mode' => "MarkDown",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back
                    ])
                ]);
            }
        }
        /*
        * الدليل
        */
        if ($text and $get_json->data == 'addhelp') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            $json_config["help"] = $text;
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم تعيين الدليل",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * إضافة رصيد
        */
        if ($text and $get_json->data == 'addbalance') {
            if(!in_array($text, $exmembers)){
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "العضو غير مووجود في قائمة الأعضااء",
                    'parse_mode' => "MarkDown",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back
                    ])
                ]);
                return;
            }
            $json["data"] = 'addbalance2';
            $json["id"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
أرسل الرصيد المراد إضافته للعضو
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }


        if ($text and $get_json->data == 'addbalance2') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            include('./sql_class.php');
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"Failed to connect to MySQL: " . mysqli_connect_error(),
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            $us = $sql->sql_select('users', 'user', $get_json->id);
            $coin = $us['coin'];
            $charge = $us['charge'];
            $fromuser = $us['fromuser'];
            if ($fromuser != 'None' && $fromuser != null){
                $us_fromuser = $sql->sql_select('users', 'user', $fromuser);
                $coin_fromuser = $us_fromuser['coin'];
                $prec_from = ($text / 100) * 2;
                $all_coin_fromuser = $us_fromuser['coinfromuser'] + $prec_from;
                $coin_fromuser_after = $prec_from + $coin_fromuser;
                $sql->sql_edit('users', 'coin', $coin_fromuser_after, 'user', $fromuser);
                $sql->sql_edit('users', 'coinfromuser', $all_coin_fromuser, 'user', $fromuser);
                bot('sendMessage', [
                    'chat_id' => $fromuser,
                    'text' => "
قام أحد الأعضاء بشحن رصيد إلى حسابه قد قمت أنت بدعوته سابقا وحصلت على نسبة 2%

الرصيد المضاف إلى حسابك : $prec_from$
رصيد دعوة الاعضاء الكلي : $all_coin_fromuser$
                    ",
                    'parse_mode' => "MarkDown",
                ]);
            }
            $vip = get_vip($charge);
            $pr = ($text / 100) * $vip;
            $af_prec = $text + $pr;
            $return = $coin + $af_prec;
            $after_charge = $charge + $text;
            $vip_after = get_vip($after_charge);
            $us = $sql->sql_edit('users', 'coin', $return, 'user', $get_json->id);
            $us = $sql->sql_edit('users', 'charge', $after_charge, 'user', $get_json->id);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
تم شحن الرصيد 

الرصيد المضاف : $text$
أصبح رصيده : $return$
حسابه : VIP$vip
نسبة الزيادة : $vip%
الزيادة : $pr$
الرصيد الكلي بعد الزيادة : $af_prec$
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
            bot('sendMessage', [
                'chat_id' => $get_json->id,
                'text' => "
تم إضافة رصيد إلى حسابك.
ــــــــــــــــــــــــــــــــــــــــــــــ
الرصيد المضاف : $text$
أصبح رصيدك : $return$
حسابك : VIP$vip
نسبة الزيادة : $vip%
الزيادة : $pr$
الرصيد الكلي بعد الزيادة : $af_prec$
                ",
                'parse_mode' => "MarkDown",
            ]);
            $gg = $get_json->id;
            bot('sendMessage', [
                'chat_id' => $dev,
                'text' => "
تم شحن رصيد.
ــــــــــــــــــــــــــــــــــــــــــــــ
الأدمن : $id
الأدمن : $first_name
إلى : $gg
الرصيد المضاف : $text$
أصبح رصيده : $return$
الرصيد الكلي بعد الزيادة : $af_prec$
                ",
                'parse_mode' => "MarkDown",
            ]);
            $best_users = explode("\n", file_get_contents('data/best_users.txt'));
            if(!in_array($get_json->id, $best_users)){
                file_put_contents('data/best_users.txt', $get_json->id."\n", FILE_APPEND);
                bot('sendMessage', [
                    'chat_id' => $get_json->id,
                    'text' => "
مرحبآ عزيزي تهانينا لقد اصبحت  مميز 😍
في حالة شحن حسابك اكثر ستحصل علئ ترقية ونسبة % في الرصيد
                    ",
                    'parse_mode' => "MarkDown",
                ]);
            }
            if($vip != $vip_after && $vip_after != 0){
                bot('sendMessage', [
                    'chat_id' => $get_json->id,
                    'text' => "
مبارك 😍
تمت ترقية مستوى حسابك VIP$vip_after

ستحصل الآن على نسبة $vip_after% عند كل عملية شحن
                    ",
                    'parse_mode' => "MarkDown",
                ]);
            }
            return;
        }

        /*
        * حذف رصيد
        */
        if ($text and $get_json->data == 'delbalance') {
            if(!in_array($text, $exmembers)){
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "العضو غير مووجود في قائمة الأعضااء",
                    'parse_mode' => "MarkDown",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $back
                    ])
                ]);
                return;
            }
            $json["data"] = 'delbalance2';
            $json["id"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
أرسل الرصيد المراد حذفه من العضو
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'delbalance2') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            include('./sql_class.php');
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"Failed to connect to MySQL: " . mysqli_connect_error(),
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            
            $us = $sql->sql_select('users', 'user', $get_json->id);
            $coin = $us['coin'];
            $return = $coin - $text;
            $us = $sql->sql_edit('users', 'coin', $return, 'user', $get_json->id);
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم إضافة الرصيد",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
            bot('sendMessage', [
                'chat_id' => $get_json->id,
                'text' => "
تم حذف رصيد من حسابك.
ــــــــــــــــــــــــــــــــــــــــــــــ
الرصيد المحذوف : $text$
أصبح رصيدك : $return$
                ",
                'parse_mode' => "MarkDown",
            ]);
            $gg = $get_json->id;
            bot('sendMessage', [
                'chat_id' => $dev,
                'text' => "
تم حذف رصيد.
ــــــــــــــــــــــــــــــــــــــــــــــ
الأدمن : $id
إلى : $gg
الرصيد المحذوف : $text$
أصبح رصيده : $return$
                ",
                'parse_mode' => "MarkDown",
            ]);
        }
        /*
        * نسبة التحويل
        */
        if ($text and $get_json->data == 'sel') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            $json_config["sel"] = $text;
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم التعيين",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
            return;
        }
        /*
        * أدنى حد للتحويل
        */
        if ($text and $get_json->data == 'selmin') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            $json_config["selmin"] = $text;
            file_put_contents("data/config.json", json_encode($json_config));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "تم التعيين",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
            return;
        }
            
        /*
        * إضافة قسم
        */

        if ($text and $get_json->data == 'addcoll') {
            $json["data"] = 'addcoll2';
            $json["name"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل وصف القسم
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'addcoll2') {
            $json["data"] = 'addcoll3';
            $json["caption"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل /ok للإضافة
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }

        if ($text == '/ok' && $get_json->data == 'addcoll3') {
            $code = rand_text();
            include("./sql_class.php");
            $sql = new mysql_api_code($db);
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"Failed to connect to MySQL: " . mysqli_connect_error(),
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            $name = $get_json->name;
            $api = $get_json->api;
            $caption = $get_json->caption;
            $sql->sql_write('buttons(code,name,caption)', "VALUES('$code','$name','$caption')");
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
تم بنجاح
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
            return;
        }
        if ($text == '/ok' && $get_json->data != 'addcoll2') {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    يا وال شو بدك تضيف!!!
    ماكو بيانات
                ",
                'parse_mode' => "MarkDown",
            ]);
        }

        /*
        * إضافة قسم عادي
        */
        if ($text and $get_json->data == 'adddivi1') {
            $json["data"] = 'adddivi2';
            $json["name"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل الآن الوصف
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }

        if ($text and $get_json->data == 'adddivi2') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            include("./sql_class.php");
            $sql = new mysql_api_code($db);
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"Failed to connect to MySQL: " . mysqli_connect_error(),
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            $code = rand_text();
            $name = $get_json->name;
            $codedivi = $get_json->codedivi;
            $caption = $text;
            $sql->sql_write('divi(code,name,codedivi,caption)', "VALUES('$code','$name', '$codedivi', '$caption')");
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    تم إضافة القسم العادي
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }


        /*
        * إضافة خدمة
        */
        if ($text and $get_json->data == 'addserv1') {
            $json["data"] = 'addserv2';
            $json["name"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل الآن رقم الخدمة
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'addserv2') {
            $json["data"] = 'addserv3';
            $json["num"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل رقم ال api  

    1,2,3...
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'addserv3') {
            $json["data"] = 'addserv4';
            $json["api"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل الآن وصف الخدمة
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'addserv4') {
            $json["data"] = 'addserv5';
            $json["caption"] = $text;
            file_put_contents("data/admin.json", json_encode($json));
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    أرسل الآن نسبة الربح 
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        if ($text and $get_json->data == 'addserv5') {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            include("./sql_class.php");
            $sql = new mysql_api_code($db);
            if (mysqli_connect_errno()) {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' =>"Failed to connect to MySQL: " . mysqli_connect_error(),
                    'parse_mode' => "MarkDown",
                    'disable_web_page_preview' => true,
                ]);
                return;
            }
            $codeserv = rand_text();
            $name = $get_json->name;
            $code = $get_json->code;
            $num = $get_json->num;
            $api = $get_json->api;
            $max = $get_json->max;
            $caption = $get_json->caption;
            $precent = $text;
            $sql->sql_write('serv(code,name,codeserv,num,api,caption,precent)', "VALUES('$code','$name', '$codeserv', '$num', '$api', '$caption','$precent')");
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "
    تم إضافة الخدمة
                ",
                'parse_mode' => "MarkDown",
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
    }
}

if ($data) {

    if(!in_array($id2, $adminss)){
        if (in_array($id2, $ex_is_no) or in_array($id2, $bans)) {
            bot('sendmessage', [
                'chat_id' => $chat_id2,
                'text' => "لا يمكنك استخدام البوت",
            ]);
            return;
        } 
    }
    /*  
    * أوامر الأدمن
    */
    if (in_array($id2, $adminss)){
        $json = json_decode(file_get_contents('data/admin.json'), true);
        $get_json = json_decode(file_get_contents('data/admin.json'));

        /*
        * تعيين start
        */
        if($data == 'addstart'){
            $json["data"] = 'addstart';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل التسارت",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * تعيين نقاط الدخول
        */
        if($data == 'addinvite'){
            $json["data"] = 'addinvite';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل نقاط الدخول",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * تعيين الدليل
        */
        if($data == 'addhelp'){
            $json["data"] = 'addhelp';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل النص الدليلي الخارق 🤣",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * تعيين قناة الاشتراك
        */
        if($data == 'addsub'){
            $json["data"] = 'addsub';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل المعرف",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * نسبة التحويل
        */
        if($data == 'sel'){
            $json["data"] = 'sel';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل التحويل",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * الحد الأدنى
        */
        if($data == 'selmin'){
            $json["data"] = 'selmin';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل الحد الأدنى",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * إضافة رصيد
        */
        if($data == 'addbalance'){
            $json["data"] = 'addbalance';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل آيدي العضو",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * حذف رصيد
        */
        if($data == 'delbalance'){
            $json["data"] = 'delbalance';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "أرسل آيدي العضو",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }

        /*
        * إضافة قسم رئيسي
        */
        if ($data == "addcoll") {
            $json["data"] = 'addcoll';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "احااا ، أرسل  أسم القسم",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * إضافة قسم عادي
        */
        if ($data == "adddivi") {
            $json["data"] = 'adddivi';
            file_put_contents("data/admin.json", json_encode($json));
            include('./sql_class.php');
            $but = $sql->sql_readarray('buttons');
            $serv = [];
            foreach ($but as $button) {
                $code = $button['code'];
                $name = $button['name'];
                $serv[] = [['text' => $name, 'callback_data' => "codedivi|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر القسم العادي",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        
        /*
        * رجوع
        */
        if ($data == "back") {
            $json["data"] = null;
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "رجوع",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $admin_button
                ])
            ]);
        }
        /*
        * إضافة خدمة
        */
        if ($data == "addserv") {
            include('./sql_class.php');
            $but = $sql->sql_readarray('divi');
            $serv = [];
            foreach ($but as $button) {
                $code = $button['code'];
                $name = $button['name'];
                $serv[] = [['text' => $name, 'callback_data' => "codeserv|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            $json["data"] = 'addserv';
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر القسم",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        /*
        * اختيار قسم رئيسي لاإضافة قسم عادي
        */
        if($exdata[0] == 'codedivi' && $get_json->data == 'adddivi'){
            $json["data"] = 'adddivi1';
            $json["codedivi"] = $exdata[1];
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "تم اختيار القسم الرئيسي, أرسل اسم القسم العادي",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * اختيار قسم لإضافة الخدمة
        */
        if($exdata[0] == 'codeserv' && $get_json->data == 'addserv'){
            $json["data"] = 'addserv1';
            $json["code"] = $exdata[1];
            file_put_contents("data/admin.json", json_encode($json));
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "تم اختيار القسم, أرسل اسم الخدمة",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }

        /*
        * حذف قسم رئيسي
        */
        if ($data == "delcoll") {
            include('./sql_class.php');
            $but = $sql->sql_readarray('buttons');
            $serv = [];
            foreach ($but as $button) {
                $code = $button['code'];
                $name = $button['name'];
                $serv[] = [['text' => $name, 'callback_data' => "delcollserv|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر القسم الرئيسي ليتم حذفه

#تحذير
عند حذف قسم رئيسي يتم حذف الأقسام و الخدمات التابعة له
                ",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        if ($exdata[0] == 'delcollserv'){
            include('./sql_class.php');
            $sql->sql_del('buttons', 'code', $exdata[1]);
            $s = $sql->sql_select_all('divi', 'codedivi', $exdata[1]);
            $arr = [];
            foreach($s as $b ){
                $c = $b['code'];
                if(in_array($c, $arr)){
                    continue;
                }
                $sql->sql_del('serv', 'code', $c);
                $arr [] = $c;
            }
            $sql->sql_del('divi', 'codedivi', $exdata[1]);
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "تم حذف القسم",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }

        /*
        * حذف قسم عادي
        */
        if ($data == "deldivi") {
            include('./sql_class.php');
            $but = $sql->sql_readarray('divi');
            $serv = [];
            foreach ($but as $button) {
                $code = $button['code'];
                $name = $button['name'];
                $serv[] = [['text' => $name, 'callback_data' => "deldiviserv|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر القسم الرئيسي ليتم حذفه

#تحذير
عند حذف قسم عادي يتم حذف الخدمات التابعة له
                ",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        if ($exdata[0] == 'deldiviserv'){
            include('./sql_class.php');
            $sql->sql_del('divi', 'code', $exdata[1]);
            $sql->sql_del('serv', 'code', $exdata[1]);
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "تم حذف القسم",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
        /*
        * حذف خدمة
        */
        if ($data == 'delserv'){
            include('./sql_class.php');
            $but = $sql->sql_readarray('divi');
            $serv = [];
            foreach ($but as $button) {
                $code = $button['code'];
                $name = $button['name'];
                $serv[] = [['text' => $name, 'callback_data' => "getserv|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر القسم",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        if ($exdata[0] == 'getserv'){
            include('./sql_class.php');
            $but = $sql->sql_select_all('serv', 'code', $exdata[1]);
            $serv = [];
            foreach ($but as $ser) {
                $code = $ser['codeserv'];
                $name = $ser['name'];
                $serv[] = [['text' => $name, 'callback_data' => "delservfromcoll|".$code]];
            }
            $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back"]];
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "اختر الخدمة ليتم حذفها",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $serv
                ])
            ]);
        }
        if ($exdata[0] == 'delservfromcoll'){
            include('./sql_class.php');
            #$sql->sql_del('buttons', 'code', $exdata[1]);
            $sql->sql_del('serv', 'codeserv', $exdata[1]);
            bot('editmessagetext', [
                'chat_id' => $chat_id2,
                'message_id' => $message_id2,
                'text' => "تم حذف الخدمة",
                'parse_mode' => "MarkDown",
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $back
                ])
            ]);
        }
    
    }
    /*  
    * أوامر الأعضاء
    */
    if($data == 'changecoin'){
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "يرجى اختيار نوع العملة",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $changecoin
            ])
        ]);
    }
    if($exdata[0] == 'selectcoin'){
        include('./sql_class.php');
        if (mysqli_connect_errno()) {
            return;
        }
        $sql->sql_edit('users', 'mycoin', $exdata[1], 'user', $id2);
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "تم اختيار نوع العملة",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $back2
            ])
        ]);
    }

    if($data == 'mystat'){
        include('./sql_class.php');
        if (mysqli_connect_errno()) {
            return;
        }
        $order_done = $sql->sql_select_all('order_done', 'user', $id2);
        $my_all = '';
        foreach($order_done as $one){
            $my_all .= $one['caption']."\n--------------\n";
        }
        file_put_contents('./files/'.$id2.'.txt', $my_all);
        #file_put_contents('./files/'.$id2.'.txt', print_r($order_done, 1));
        if($my_all == ''){
            bot('answerCallbackQuery',[
                'callback_query_id'=>$update->callback_query->id,
                'text'=>"⇜ ليس لديك طلبات حاليا ", 
                'show_alert'=>true,
                'cache_time'=> 20
            ]);
            return;
        }
        bot('answerCallbackQuery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"✅ ستصلك التفاصيل بعد قليل", 
            'show_alert'=>true,
            'cache_time'=> 20
        ]);
        bot('sendDocument', [
            'chat_id' => $chat_id2,
            'document' => new CURLFILE('./files/'.$id2.'.txt')
        ]);
        unlink('./files/'.$id2.'.txt');
        return;
    }
    if($run == 'stop' && !in_array($id2, $adminss)){
        bot('answerCallbackQuery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"البوت معطل حاليا...", 
            'show_alert'=>true,
            'cache_time'=> 20
        ]);
        return;
    }
    if($data == 'back2'){
        $jsons["$id2"] = null;
        file_put_contents("data/data.json", json_encode($jsons));
        include('./sql_class.php');
        $sq = $sql->sql_select('users', 'user', $id2);
        $coin = $sq['coin'];
        $mycoin = $sq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_after_coin = $info_coin[0] * $coin;
        $coin_name = $info_coin[1];
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "
 🙋🏻‍♂️  مرحبآ عزيزي 
ـــــــــــــــــــــــــــــــــــــــــــــ
↤ بوت خدمات الراقي للرشق
↤ هو البوت الأكثر تميزاً 
↤ لمساعدتك في رفع متابعينك        
↤ في جميع مواقع التواصل الاجتماعي
↤ متابعين + لايكات + مشاهدات
↤ بكل سهولة وامان

💵 رصيدك : *$coin_after_coin *$coin_name
ــــــــــــــــــــــــــــــــــــــــــــــ
            ",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $start
            ])
        ]);
    }
    if($data == 'help'){
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => $config->help,
            'reply_markup' => json_encode([
                'inline_keyboard' => $back2
            ])
        ]);
    }
    /**
     * تحويل نقاط
     */
    if($data =='sendmoney'){
        $jsons["$id2"]["data"] = 'sendmoney';
        file_put_contents("data/data.json", json_encode($jsons));
        $min = $config->selmin;
        $prec = $config->sel;
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "
يجب أن يكون العضو المراد التحويل له مشتركا في البوت

تحويل الرصيد بالدولار فقط..
قم الآن بإرسال الايدي الخاص به
عمولة التحويل : $prec%
أدنى حد للتحويل : $min$
            ",
            'reply_markup' => json_encode([
                'inline_keyboard' => $back2
            ])
        ]);
    }
    if($data == 'myaccount'){
        $back_add = [
            [['text' => "إلغاء ورجوع", 'callback_data' => "back2"]],
        ];
        $all = count($exmembers);
        $best_userss = explode("\n", file_get_contents('data/best_users.txt'));
        if(in_array($id2, $best_userss)){
            $me = "العضوية :  مميز";
        }else{
            $me = "العضوية :  عادي";
        }
        $best_users = count($best_userss) ?? 0;
        include('./sql_class.php');
        if (mysqli_connect_errno()) {
            return;
        }
        $coin_users = $sql->sql_readarray('users');
        $coin_all = 0;
        $coin_spent = 0;
        foreach($coin_users as $coins){
            $coin = $coins['coin'];
            $spent = $coins['spent'];
            $user = $coins['user'];
            $charge = $coins['charge'];
            if($id2 == $user){
                $us_coin = $coin;
                $us_spent = $spent;
                $us_charge = $charge;
            }
            $coin_all += $coin;
            $coin_spent += $spent;
        }
        $vip = get_vip($us_charge);
        $done = $sql->sql_readarray_count('order_done');
        $waiting = $sql->sql_readarray_count('order_waiting');
        $order_done = count($sql->sql_select_all('order_done', 'type', 'Completed'));
        $order_Canceled = count($sql->sql_select_all('order_done', 'type', 'Canceled')) ?? 0;
        $order_Partial = count($sql->sql_select_all('order_done', 'type', 'Partial')) ?? 0;
        $all_order = $done + $waiting;

        $order_user = $sql->sql_select_all('order_done', 'user', $id2);
        $us_done = 0;
        $us_cans = 0;
        $us_part = 0;
        
        foreach($order_user as $od_us){
            if($od_us['type'] == 'Completed'){
                $us_done += 1;
            }
            if($od_us['type'] == 'Canceled'){
                $us_cans += 1;
            }
            if($od_us['type'] == 'Partial'){
                $us_part += 1;
            }
        }
        $us_all = $us_done + $us_cans + $us_part;
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "
            🙋🏻‍♂️ مرحبآ عزيزي : $first_name
ــــــــــــــــــــــــــــــــــــــــــــــ
👥 عدد عملاء البوت : $all
💸 الرصيد المتوفر : $coin_all$
💸 الرصيد المستهلك : $coin_spent$
📊 عدد الطلبات الكلي : $all_order
✅ الطلبات المكتملة : $order_done
❌ الطلبات الملغيه : $order_Canceled
♻️ الطلبات المكتملة جزئيا : $order_Partial
⏳ الطلبات الجاري تنفيذها : $waiting
ــــــــــــــــــــــــــــــــــــــــــــــ
            ",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $back_add
            ])
        ]);
    }
    if($data == 'my'){
                $back_add = [
                [['text' => "طلباتي بالتفصيل 📜", 'callback_data' => "mystat"]],
            [['text' => "إلغاء ورجوع", 'callback_data' => "back2"]],
        ];
        $all = count($exmembers);
        $best_userss = explode("\n", file_get_contents('data/best_users.txt'));
        if(in_array($id2, $best_userss)){
            $me = "العضوية :  مميز 🏅";
        }else{
            $me = "العضوية :  عادي 🥈";
        }
        $best_users = count($best_userss) ?? 0;
        include('./sql_class.php');
        if (mysqli_connect_errno()) {
            return;
        }
        $coin_users = $sql->sql_readarray('users');
        $coin_all = 0;
        $coin_spent = 0;
        foreach($coin_users as $coins){
            $coin = $coins['coin'];
            $spent = $coins['spent'];
            $user = $coins['user'];
            $charge = $coins['charge'];
            $coinfromuser = $coins['coinfromuser'];
            if($id2 == $user){
                $us_coin = $coin;
                $us_spent = $spent;
                $us_charge = $charge;
                $coin_from_user = $coinfromuser;
            }
            $coin_all += $coin;
            $coin_spent += $spent;
        }
        $vip = get_vip($us_charge);
        $done = $sql->sql_readarray_count('order_done');
        $waiting = $sql->sql_readarray_count('order_waiting');
        $order_done = count($sql->sql_select_all('order_done', 'type', 'Completed'));
        $order_Canceled = count($sql->sql_select_all('order_done', 'type', 'Canceled')) ?? 0;
        $order_Partial = count($sql->sql_select_all('order_done', 'type', 'Partial')) ?? 0;
        $all_order = $done + $waiting;

        $order_user = $sql->sql_select_all('order_done', 'user', $id2);
        $us_done = 0;
        $us_cans = 0;
        $us_part = 0;
        foreach($order_user as $od_us){
            if($od_us['type'] == 'Completed'){
                $us_done += 1;
            }
            if($od_us['type'] == 'Canceled'){
                $us_cans += 1;
            }
            if($od_us['type'] == 'Partial'){
                $us_part += 1;
            }
        }
        $us_all = $us_done + $us_cans + $us_part;

        $sqsq = $sql->sql_select('users', 'user', $id2);
        $mycoin = $sqsq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_name = $info_coin[1];
        $us_coin2 = $us_coin * $info_coin[0];
        $us_spent2 = $us_spent * $info_coin[0];
        $us_charge2 = $us_charge * $info_coin[0];
        $coin_from_user2 = $coin_from_user * $info_coin[0];

        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "
🙋🏻‍♂️ مرحبآ عزيزي : $first_name
ــــــــــــــــــــــــــــــــــــــــــــــ
♻️ معلومات حسابك 
ــــــــــــــــــــــــــــــــــــــــــــــ
🆔 ايدي حسابك : $id2
🎫 $me
🏅 مستوى الحساب : VIP$vip
💸 رصيدك المتوفر : $us_coin2 $coin_name
💸 رصيد دعوة الاعضاء الكلي : $coin_from_user2 $coin_name
💸 رصيدك المستهلك : $us_spent2 $coin_name
💲رصيدك المشحون : $us_charge2 $coin_name
📊 عدد طلباتك : $us_all
✅ طلباتك المكتملة : $us_done
❌ طلباتك الملغيه : $us_cans
⏳ طلبات بالتنفيذ : $us_part
ــــــــــــــــــــــــــــــــــــــــــــــ
🖇️ رابطك الخاص : $link_invite$id2
༄
            ",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $back_add
            ])
        ]);
    }
    if ($data == 'done' && $get_jsons->{$id2}->data == 'done'){
        $jsons["$id2"] = null;
        file_put_contents("data/data.json", json_encode($jsons));
        $best_users = explode("\n", file_get_contents('data/best_users.txt'));
        $user_one_dollar = explode("\n", file_get_contents('data/user_one_dollar.txt'));
        if(!in_array($id2, $best_users) and !in_array($id2, $user_one_dollar)){
            bot('answerCallbackQuery',[
                'callback_query_id'=>$update->callback_query->id,
                'text'=>"لايمكنك الرشق حاليا حتى يصل رصيدك إلى 1$ على الأقل", 
                'show_alert'=>true,
                'cache_time'=> 20
            ]);
            return;
        }
        bot('answerCallbackQuery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"تم تأكيد الطلب، سأخبرك بالتفاصيل قريبا...", 
            'show_alert'=>true,
            'cache_time'=> 20
        ]);
        $serv = $get_jsons->{$id2}->serv;
        $codeserv = $get_jsons->{$id2}->codeserv;
        $num_order  = $get_jsons->{$id2}->num;
        $price_order = $get_jsons->{$id2}->price_order;
        $price_k = $get_jsons->{$id2}->price_k;
        $link = $get_jsons->{$id2}->link;
        include('./sql_class.php');
        if (mysqli_connect_errno()) {
            bot('sendMessage', [
                'chat_id' => $chat_id2,
                'text' =>"
حدث خطأ أثناء المعالجة وتم إلغاء الطلب..

رجاء حاول لاحقا
                ",
                'disable_web_page_preview' => true,
            ]);
            return;
        }
        $sq = $sql->sql_select('users', 'user', $id2);
        $sq22 = $sql->sql_select('serv', 'codeserv', $codeserv);
        $apis = $sq22['api'];
        $name = $sq22['name'];
        $num = $sq22['num'];
        $coin = $sq['coin'];
        $spent = $sq['spent'] + $price_order;
        $coin_after = $coin - $price_order;

        $sqsq = $sql->sql_select('users', 'user', $id2);
        $mycoin = $sqsq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_name = $info_coin[1];

        $price_k2 = $price_k * $info_coin[0];
        $price_order2 = $price_order * $info_coin[0];
        $coin2 = $coin * $info_coin[0];
        $coin_after2 = $coin_after * $info_coin[0];
        include_once('apifiles/'.$apis.".php");
        if ($apis == '1'){
            $api = new Api();
        }
        if ($apis == '2'){
            $api = new Api2();
        }
        if ($apis == '3'){
            $api = new Api3();
        }
        #$api = new Api();
        $balance = json_decode(json_encode($api->balance()))->balance;
        $order = $api->order(array('service' => $num, 'link' => $link, 'quantity' => $num_order));
        $order_js = json_decode(json_encode($order));
        $order_id = $order_js->order;
        if($order_js->error){
            $error = $order->error;
            bot('sendMessage', [
                'chat_id' => $chat_id2,
                'text' =>"
نعتذر حدث خطأ من المصدر وتم إلغاء الطلب
تم إبلاغ الإدارة ليتم مراجعة الخدمة
                ",
                'disable_web_page_preview' => true,
            ]);
            foreach($adminss as $one){
                bot('sendMessage', [
                    'chat_id' => $one,
                    'text' =>"
حدث خطأ في الخدمة ذات الرقم : $num
اسم الخدمة : $name
الapi : $apis
الخطأ : $error
                    ",
                    'disable_web_page_preview' => true,
                ]);
            }
            return;
        }
        $sql->sql_edit('users', 'coin', $coin_after, 'user', $id2);
        $sql->sql_edit('users', 'spent', $spent, 'user', $id2);

        $mm = $sql->sql_readarray_count('order_waiting') + $sql->sql_readarray_count('order_done') + 1;
        #$order_id = '1000';
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "تم تأكيد الطلب",
        ]);
        $tlb = "[#طلب_جديد]";
        $cap = "
ــــــــــــــــــــــــــــــــــــــــــــــ
✅| الخدمة : $name
💳| كود الخدمة : $codeserv
🎰| رقم الطلب : $mm
💵| السعر لكل : *1k*
*「$price_k2 $coin_name 」*
🧮| العدد المطلوب : *$num_order*
💸| اجمالي السعر
*「$price_order2 $coin_name -  $price_order دولار」*
ــــــــــــــــــــــــــــــــــــــــــــــ
";
$cap2 = "
ــــــــــــــــــــــــــــــــــــــــــــــ
*📜 الخدمة : $name*
*♻️ كود الخدمة : *$codeserv
*🎰 رقم الطلب : $mm*
*💲سعر الف عضو : $price_k2 $coin_name
🧮 العدد المطلوب : $num_order
💸 تم خصم : $price_order2 $coin_name
💸 رصيدك قبل الطلب : $coin2 $coin_name
💸 رصيدك بعد الطلب  : $coin_after2 $coin_name*
🖇️ الرابط : $link
ــــــــــــــــــــــــــــــــــــــــــــــ
";
        $cap_for_admin = "
#طلب_جديد
اسم العضو : $first_name
آيدي العضو : $id2
الخدمة : $name
كود الخدمة : $codeserv
رقم الطلب : $mm
سعر الف عضو : $price_k$
سعر الف عضو : $price_k2 $coin_name
العدد المطلوب : $num_order
السعر الكلي : $price_order$
السعر الكلي : $price_order2 $coin_name
رقم الخدمة : $num
ال api : $apis
رصيد العضو قبل الطلب : $coin$
رصيد العضو بعد الطلب  : $coin_after$
رصيد العضو قبل الطلب : $coin2 $coin_name
رصيد العضو بعد الطلب  : $coin_after2 $coin_name
رصيدك بالموقع $apis : $balance$
الرابط : $link
";
        $stut = '⏳ حالة الطلب : جار التنفيذ...';
        $for_user = bot('sendMessage', [
            'chat_id' => $chat_id2,
            'text' => $tlb.$cap2."".$stut,
            'disable_web_page_preview' => true,
        ]);
        $f_user = $for_user->result->message_id;
        $for_chat = bot('sendMessage', [
            'chat_id' => $ch,
            'text' => $tlb.$cap."".$stut,
            'parse_mode'=>'markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $my_bot
            ])
        ]);
        $f_chat = $for_chat->result->message_id;
        $sql->sql_write('order_waiting(user,caption,ms_user,ms_channel,order_id,api,price,num_order,link)', "VALUES('$id2','$cap','$f_user', '$f_chat','$order_id','$apis','$price_order','$num_order','$link')");
        foreach($adminss as $one){
            bot('sendMessage', [
                'chat_id' => $one,
                'text' => $cap_for_admin."".$stut,
                'disable_web_page_preview' => true,
            ]);
        }

    }
    if ($data == 'done' && $get_jsons->{$id2}->data != 'done'){
        bot('answerCallbackQuery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"لا يتوفر خدمة مضافة، قم بطلب الخدمة مرة أخرى", 
            'show_alert'=>true,
            'cache_time'=> 20
        ]);
        return;
    }
    if($data == 'addusers'){
        $jsons["$id2"] = null;
        file_put_contents("data/data.json", json_encode($jsons));
        include('./sql_class.php');
        $but = $sql->sql_readarray('buttons');
        $serv = [];
        foreach ($but as $button) {
            $code = $button['code'];
            $name = $button['name'];
            $serv[] = [['text' => $name, 'callback_data' => "selcetdivi|".$code]];
        }
        $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "back2"]];
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => "مرحبآ عزيزي 
ـــــــــــــــــــــــــــــــــــــــــــــ
↤اختر من الاقسام في الاسفل        
↤جميع مواقع التواصل الاجتماعي
↤ متابعين + لايكات + مشاهدات
↤ رياكشنات + تعليقات + 
༄",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $serv
            ])
        ]);
    }
    if($exdata[0] == 'selcetdivi'){
        include('./sql_class.php');
        $but = $sql->sql_select_all('divi', 'codedivi', $exdata[1]);
        $serv = [];
        foreach ($but as $button) {
            $code = $button['code'];
            $name = $button['name'];
            $cap = $button['caption'];
            $serv[] = [['text' => $name, 'callback_data' => "selcetcoll|".$code]];
        }
        $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "addusers"]];
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => $cap,
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $serv
            ])
        ]);
    }

    if ($exdata[0] == 'selcetcoll'){
        bot('answerCallbackQuery',[
            'callback_query_id'=>$update->callback_query->id,
            'text'=>"⇜ جار تحميل الخدمات..", 
            'show_alert'=>true,
            'cache_time'=> 20
        ]);
        
        include('./sql_class.php');
        $but = $sql->sql_select_all('serv', 'code', $exdata[1]);
        $qq = $sql->sql_select('divi', 'code', $exdata[1]);

        $sq = $sql->sql_select('users', 'user', $id2);
        $mycoin = $sq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_name = $info_coin[1];

        $cap = $qq['caption'];
        #return;
        $serv = [];
        $serv[] = [['text' => "الخدمة 📮", 'callback_data' => "no"]];
        $serv[] = [['text' => 'سعر 1k 💸', 'callback_data' => "no"], ['text' => "ادنى ⇆ اقصى 📈", 'callback_data' => "no"]];
        foreach ($but as $ser) {
            $code = $ser['codeserv'];
            $name = $ser['name'];
            $num = $ser['num'];
            $apis = $ser['api'];
            $prec_c = $ser['precent'];
            $g = get_serv($apis, $num);
            if(!$g){
                continue;
            }
            $rate = $g['rate'];
            #$sq = $sql->sql_select('serv', 'codeserv', $code);
            $price = ((($rate / 100) * $prec_c) + $rate) * $info_coin[0];
            $min = $g['min'];
            $max = $g['max'];
            $serv[] = [['text' => $name.' 📮', 'callback_data' => "selcetserv|".$num."|".$code]];
            $serv[] = [['text' => $price.'↤'.$coin_name, 'callback_data' => "selcetserv|".$num."|".$code], ['text' => shortNumber($max).' ⇆ '.shortNumber($min).' 📈', 'callback_data' => "selcetserv|".$num."|".$code]];
            $g = '';
        }
        $serv[] = [['text' => "إلغاء ورجوع", 'callback_data' => "addusers"]];
        $v = bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => $cap,
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $serv
            ])
        ]);
        #file_put_contents('t.txt', 'sss', FILE_APPEND);

    }

    if($exdata[0] == 'selcetserv'){
        $jsons["$id2"]["data"] = 'link';
        $jsons["$id2"]["serv"] = $exdata[1];
        $jsons["$id2"]["codeserv"] = $exdata[2];
        file_put_contents("data/data.json", json_encode($jsons));
        include('./sql_class.php');
        $sq = $sql->sql_select('serv', 'codeserv', $exdata[2]);
        $cap = $sq['caption'];
        $prec_c = $sq['precent'];
        $num = $sq['num'];
        $apis = $sq['api'];

        $sqsq = $sql->sql_select('users', 'user', $id2);
        $mycoin = $sqsq['mycoin'];
        $info_coin = get_coin_info($mycoin);
        $coin_name = $info_coin[1];

        $g = get_serv($apis, $num);
        $rate = $g['rate'];
        #$sq = $sql->sql_select('serv', 'codeserv', $code);
        $price = ((($rate / 100) * $prec_c) + $rate) * $info_coin[0];
        $min = shortNumber($g['min']);
        $max = shortNumber($g['max']);
        $ms = "
 💲السعر لكل الف عضو : $price $coin_name
⬇️ الحد الأدنى للطلب : $min
⬆️ الحد الأقصى للطلب : $max
        ";
        bot('editmessagetext', [
            'chat_id' => $chat_id2,
            'message_id' => $message_id2,
            'text' => $cap."\n".$ms,
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $back_add
            ])
        ]);
    }
}

