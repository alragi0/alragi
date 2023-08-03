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
    file_put_contents('error.txt', $res."\n", FILE_APPEND);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
        $res = json_decode($res);
        return $res;
    } else {
        $res = json_decode($res);
        return $res;
    }
}


include('./sql_class.php');
if (mysqli_connect_errno()) {
    return;
}
$sq = array_reverse($sql->sql_readarray('order_waiting'));
shuffle($sq);
$t = '0';
foreach($sq as $array){
    $t += 1;
    $user = $array['user'];
    $cap = $array['caption'];
    $ms_user = $array['ms_user'];
    $ms_channel = $array['ms_channel'];
    $order_id = $array['order_id'];
    $price = $array['price'];
    $num_order = $array['num_order'];
    $apis = $array['api'];
    require_once('apifiles/'.$apis.".php");
    if($apis == '1'){
        $api = new Api();
    }elseif($apis == '2'){
        $api = new Api2();
     }elseif($apis == '3'){
        $api = new Api3();
    }
    $status = json_decode(json_encode($api->status($order_id)));

    /**
     * In progress
     * Partial
     * Completed
     * Canceled
     * {"charge":"0.14","start_count":null,"status":"Completed","remains":"0","currency":"USD"}
     */
    $stut = $status->status;

    /**
     * Canceled
     */
    if ($stut == 'Canceled'){
        $us = $sql->sql_select('users', 'user', $user);
        $coin = $us['coin'];
        $sp = $us['spent'];
        $return = $coin + $price;
        $spent = $sp - $price;
        $us = $sql->sql_edit('users', 'coin', $return, 'user', $user);
        $us = $sql->sql_edit('users', 'spent', $spent, 'user', $user);
        $sql->sql_del('order_waiting', 'order_id', $order_id);
        $capt = "[#طلب_ملغي]".$cap."\nتم استرجاع الرصيد : $price";
       
        #user	type	caption	api	price	remains	order_id
        $sql->sql_write('order_done(user,type,caption,api,price,order_id,remains,num_order)', "VALUES('$user','$stut','$capt','$apis','$price','$order_id','0','$num_order')");     
        bot('editmessagetext', [
            'chat_id' => $ch,
            'message_id' => $ms_channel,
            'text' => $capt, 
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $my_bot
            ])
        ]);
        bot('sendMessage', [
            'chat_id' => $user,
            'text' => $capt,
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_to_message_id'=>$ms_user,
        ]);
        foreach($adminss as $one){
            bot('sendMessage', [
                'chat_id' => $one,
                'text' => $capt,
                'parse_mode'=>markdown,
                'disable_web_page_preview' => true,
            ]);
        }
    }

    /**
     * Completed
     */
    if ($stut == 'Completed'){
        $capt = "[#طلب_مكتمل]".$cap;
       
        $sql->sql_write('order_done(user,type,caption,api,price,order_id,remains,num_order)', "VALUES('$user','$stut','$capt','$apis','$price','$order_id','0','$num_order')");  
        $sql->sql_del('order_waiting', 'order_id', $order_id);   
        bot('editmessagetext', [
            'chat_id' => $ch,
            'message_id' => $ms_channel,
            'text' => $capt,
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $my_bot
            ])
        ]);
        bot('sendMessage', [
            'chat_id' => $user,
            'text' => $capt,
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_to_message_id'=>$ms_user,
        ]);
        foreach($adminss as $one){
            bot('sendMessage', [
                'chat_id' => $one,
                'text' => $capt,
                'parse_mode'=>markdown,
                'disable_web_page_preview' => true,
            ]);
        }
    }

    /**
     * Partial
     */
    if ($stut == 'Partial'){
        $remains = $status->remains;
        $one_member = $price / $num_order;
        $price_remains = $remains * $one_member;
        $us = $sql->sql_select('users', 'user', $user);
        $coin = $us['coin'];
        $sp = $us['spent'];
        $return = $coin + $price_remains;
        $spent = $sp - $price_remains;
        $us = $sql->sql_edit('users', 'coin', $return, 'user', $user);
        $us = $sql->sql_edit('users', 'spent', $spent, 'user', $user);
        $capt = "[#طلب_مكتمل_جزئيا]".$cap."\nالمتبقي : $remains\nتم استرجاع رصيد الاعضاء المتبقي";
        $capt2 = $capt."\nتم إسترجاع : $price_remains$";
        #user	type	caption	api	price	remains	order_id
        $sql->sql_write('order_done(user,type,caption,api,price,order_id,remains,num_order)', "VALUES('$user','$stut','$capt2','$apis','$price','$order_id','$remains','$num_order')");     
        $sql->sql_del('order_waiting', 'order_id', $order_id);
        bot('editmessagetext', [
            'chat_id' => $ch,
            'message_id' => $ms_channel,
            'text' => $capt2,
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => $my_bot
            ])
        ]);
        bot('sendMessage', [
            'chat_id' => $user,
            'text' => $capt2,
            'parse_mode'=>markdown,
            'disable_web_page_preview' => true,
            'reply_to_message_id'=>$ms_user,
        ]);
        foreach($adminss as $one){
            bot('sendMessage', [
                'chat_id' => $one,
                'text' => $capt2,
                'parse_mode'=>markdown,
                'disable_web_page_preview' => true,
            ]);
        }
    }

    if($t == 5){
        break;
    }
}
