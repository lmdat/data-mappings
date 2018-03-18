<?php namespace App\Libs\Utils;


class Vii{

    public static function shortid($x=2, $y=4, $len=8){
        if($x + $y > $len)
            return false;
        
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '1234567890';

        $l = substr(str_shuffle($letters), 0, $x);
        $n = substr(str_shuffle($digits), 0, $y);
        
        return $l . $n;
            
    }
    
    public static function guid($domain='vii', $brace=false, $dash=true){
        
        if (function_exists('com_create_guid')){
            $guid = com_create_guid();
            
            if(!$dash)
                $guid = str_replace('-', '', $guid);
            if(!$brace)
                $guid = trim($guid, '{}');
            
            return strtolower($guid);
        }
                
        $src = sha1(uniqid($domain) . time() . ((double)microtime() * 10000));
        $hyphen = ($dash == true) ? '-' : '';
        $guid = substr($src, 0, 8) . $hyphen
                .substr($src, 8, 4) . $hyphen
                .substr($src, 12, 4) . $hyphen
                .substr($src, 16, 4) . $hyphen
                .substr($src, 20, 12);

        return ($brace == true) ? '{' . $guid . '}' : $guid;
    }
    
    public static function pr($obj, $is_file=false, $is_append=false){
        ob_start();
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
        if($is_file !== false && is_string($is_file)){
            $v = ob_get_contents();
            if(!$is_append)
                file_put_contents($is_file, $v);
            else
                file_put_contents($is_file, $v, FILE_APPEND);
            ob_end_clean(); 
        }
    }
    
    public static function createOTP($key_len=9, $code_len=7){
		
		$rs = [];

		$_range = [];
		for($j=0;$j<($key_len/3);$j++){
			while(true){
				$num = mt_rand(100, 999);
				if(!in_array($num, $_range)){
					$_range[] = $num;
					break;
				}
			}
		}
        
		shuffle($_range);
        $_key = implode('', $_range);
		$rs['otp_key'] = $_key;
        
		$_code = '';
		for($i=0;$i<$code_len;$i++){
			$k = 0;
			if($i % 2 == 1)
				$k = 1;
			$_code .= mt_rand($k, 9);
		}
		
		$rs['otp_code'] = $_code;
        $rs['otp_hash'] = sha1($_key . '@' . $_code);
		return $rs;
	}	
    
    public static function getMailer($cfg=null){
        
        $SECURE_TYPE = [
            465 => 'ssl',
            587 => 'tls'
         ];
        
        if($cfg == null){
            //use App\Models\Setting;
            $setting = \App\Models\Setting::find(1);
            $setting_data = json_decode($setting->setting_data, true);
            if(!isset($setting_data['smtp']))
                return null;
            
            $cfg = $setting_data['smtp']; 
        }
                        
        $mailer = new \PHPMailer();
        $mailer->isSMTP();
        $mailer->CharSet = 'utf-8';
        
        $mailer->Host = $cfg['host_name'];  // Specify main and backup SMTP servers
        $mailer->Port = intval($cfg['port']);
        if($cfg['secure'] == 'ssl'){
            $mailer->SMTPSecure = $SECURE_TYPE[$mailer->Port];
            //$mailer->SMTPSecure = 'tls';
        }
        
        
        if($cfg['username'] != '' && $cfg['password'] != ''){
            $mailer->SMTPAuth = true;               // Enable SMTP authentication
            $mailer->Username = $cfg['username'];   // SMTP username
            $mailer->Password = $cfg['password'];
        }
        $mailer->From = $cfg['email_sender'];
        $mailer->FromName = $cfg['sender_name'];
        
        return $mailer;
        
    }
    
    public static function queryStringBuilder($items){
        
        if(is_string($items)){
            return sprintf('?%s', $items);    
        }
        
        if(is_array($items) && empty($items))
            return '';
        
        $qs = [];
        foreach($items as $k => $v){
            $qs[] = sprintf('%s=%s', $k,urlencode($v));
        }
        
        return sprintf('?%s', implode('&', $qs));
    }
    
    public static function getStatusTaskName(){
        return ['0'=>'Pending', '1'=>'Ongoing', '2'=>'Completed'];
    }
    
    public static function createOptionData($data, $field_value, $field_text, $empty=['' => '---']){
        
        $rs = [];
        if(is_array($empty))
            $rs = $empty;
        
        foreach($data as $k => $v){
            
            if(is_array($field_text)){
                $text = [];
                foreach($field_text as $f)
                    $text[] = $v[$f];

                $rs[$v[$field_value]] = implode(' | ', $text);
            }
            else{
                $rs[$v[$field_value]] = $v[$field_text];
            }
            
        }
        
        return $rs;
    }

    public static function createOptionTags($data){

        $rs = [];

        foreach($data as $k => $v){
            $rs[] = '<option value="'.$k.'">'.$v.'</option>';
        }

        return implode('', $rs);
    }


    public static function createCheckboxOrRadioData($data, $field_value, $field_text, $type='checkbox', $inline=false){

        $rs = [];
        if(!$inline){
            foreach($data as $k => $v){

                $_text = $v[$field_text];
                $_val = $v[$field_value];

                $tpl = "<div class='$type'>";
                $tpl .= "   <label>";
                $tpl .= "       <input type='$type' name='role_id[]' id='role-$_val' value='$_val'> $_text";
                $tpl .= "   </label>";
                $tpl .= "</div>";

                $rs[] = $tpl;
            }
        }

        return $rs;
    }
    
    // public static function unaccent($s){
	// 	$arr = array(
	// 			'Á'=>'A', 'À'=>'A', 'Ả'=>'A', 'Ã'=>'A', 'Ạ'=>'A',
	// 			'Ấ'=>'A', 'Ầ'=>'A', 'Ẩ'=>'A', 'Ẫ'=>'A', 'Ậ'=>'A',
	// 			'Ắ'=>'A', 'Ằ'=>'A', 'Ẳ'=>'A', 'Ẵ'=>'A', 'Ặ'=>'A',
	// 			'Â'=>'A', 'Ă'=>'A',
	// 			'á'=>'a', 'à'=>'a', 'ả'=>'a', 'ã'=>'a', 'ạ'=>'a',
	// 			'ấ'=>'a', 'ầ'=>'a', 'ẩ'=>'a', 'ẫ'=>'a', 'ậ'=>'a',
	// 			'ắ'=>'a', 'ằ'=>'a', 'ẳ'=>'a', 'ẵ'=>'a', 'ặ'=>'a',
	// 			'â'=>'a', 'ă'=>'a',
	
	// 			'Ó'=>'O', 'Ò'=>'O', 'Ỏ'=>'O', 'Õ'=>'O', 'Ọ'=>'O',
	// 			'Ố'=>'O', 'Ồ'=>'O', 'Ổ'=>'O', 'Ỗ'=>'O', 'Ộ'=>'O',
	// 			'Ớ'=>'O', 'Ờ'=>'O', 'Ở'=>'O', 'Ỡ'=>'O', 'Ợ'=>'O',
	// 			'Ô'=>'O',
	// 			'Ơ'=>'O',
	// 			'ó'=>'o', 'ò'=>'o', 'ỏ'=>'o', 'õ'=>'o', 'ọ'=>'o',
	// 			'ố'=>'o', 'ồ'=>'o', 'ổ'=>'o', 'ỗ'=>'o', 'ộ'=>'o',
	// 			'ớ'=>'o', 'ờ'=>'o', 'ở'=>'o', 'ỡ'=>'o', 'ợ'=>'o',
	// 			'ô'=>'o',
	// 			'ơ'=>'o',
	
	// 			'Ú'=>'U', 'Ù'=>'U', 'Ủ'=>'U', 'Ũ'=>'U', 'Ụ'=>'U',
	// 			'Ứ'=>'U', 'Ừ'=>'U', 'Ử'=>'U', 'Ữ'=>'U', 'Ự'=>'U',
	// 			'Ư'=>'U',
	// 			'ú'=>'u', 'ù'=>'u', 'ủ'=>'u', 'ũ'=>'u', 'ụ'=>'u',
	// 			'ứ'=>'u', 'ừ'=>'u', 'ử'=>'u', 'ữ'=>'u', 'ự'=>'u',
	// 			'ư'=>'u',
	
	// 			'É'=>'E', 'È'=>'E', 'Ẻ'=>'E', 'Ẽ'=>'E', 'Ẹ'=>'E',
	// 			'Ế'=>'E', 'Ề'=>'E', 'Ể'=>'E', 'Ễ'=>'E', 'Ệ'=>'E',
	// 			'Ế'=>'E',
	// 			'é'=>'e', 'è'=>'e', 'ẻ'=>'e', 'ẽ'=>'e', 'ẹ'=>'e',
	// 			'ế'=>'e', 'ề'=>'e', 'ể'=>'e', 'ễ'=>'e', 'ệ'=>'e',
	// 			'ê'=>'e',
	
	// 			'Í'=>'I', 'Ì'=>'I', 'Ỉ'=>'I', 'Ĩ'=>'I', 'Ị'=>'I',
	// 			'í'=>'i', 'ì'=>'i', 'ỉ'=>'i', 'ĩ'=>'i', 'ị'=>'i',
				
	// 			'Ý'=>'Y', 'Ỳ'=>'Y', 'Ỷ'=>'Y', 'Ỹ'=>'Y', 'Ỵ'=>'Y',
	// 			'ý'=>'y', 'ỳ'=>'y', 'ỷ'=>'y', 'ỹ'=>'y', 'ỵ'=>'y',
	
	// 			'Đ'=>'D', 'đ'=>'d'
	
	// 	);
        	
	// 	return strtr($s, $arr);
    // }
    
    public static function sanitize($str) {
        if(!$str) return '';

        $utf8 = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd'=>'đ',
            'D'=>'Đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        ];

        foreach($utf8 as $k => $v){
            $str = preg_replace("/($v)/i", $k, $str);
        }

        return $str;
    }
    
    public static function makeAlias($s){
        //$pattern = array('~', '`', ':', '.', ',', '=', '<', '>', ';', '?', '!', '+', "'", '"', '@', '#', '$', '&', '*', '%', '^', '(', ')', '?', '/', '\\');
        $pattern = ['‘', '’'];
        for($i=33; $i<=126; $i++){
            if($i == 45 || $i == 95 || ($i >= 65 && $i <=90) || ($i >= 97 && $i <= 122) || ($i >= 48 && $i <= 57))
                continue;
            $pattern[] = chr($i);
        }

        $s = self::sanitize($s);
        $s = str_replace($pattern, '', trim($s));
        return str_replace(['_', ' '], '-', strtolower(rtrim(ltrim($s))));
    }

		
    public static function makeSearchWordsRaw($fields=array(), $str_search){

        $cond = array();
        $words = explode(' ', $str_search);
        for($i=0;$i<count($fields);$i++){
            $field = $fields[$i];
            $temp['AND'] = array();
            foreach($words as $word){
                $temp['AND'][] = array("$field LIKE" => "%$word%");
            }

            $cond[] = $temp;

        }

        return $cond;
    }
    
    public static function makeSearchWords($q, $fields=array(), $str_search){

        if(is_string($str_search))
            $words = explode(' ', ltrim(rtrim(trim($str_search))));

        if(is_array($str_search))
            $words = $str_search;

        $q->where(function($q) use($fields, $words){

            for($i=0;$i<count($fields);$i++){
                $field = $fields[$i];
                foreach($words as $word){
                    $q->orWhere($field, 'LIKE', "%".$word."%");
                }
            }
        });


        return $q;
    }

    public static function makeSearchExactWords($q, $fields=array(), $str_search){

        if(is_string($str_search))
            $words = explode(' ', ltrim(rtrim(trim($str_search))));

        if(is_array($str_search))
            $words = $str_search;

        $q->where(function($q) use($fields, $words){

            for($i=0;$i<count($fields);$i++){
                $field = $fields[$i];

                foreach($words as $word){
                    $q->orWhere($field, 'RLIKE', "[[:<:]]".$word."[[:>:]]");
                }
            }
        });


        return $q;
    }
    
    public static function isMobileBrowser(){
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) return true;

        return false;
    }
    
   
    public static function getDayName($n){
        $arr = [
            '0' => 'Chủ Nhật',
            '1' => 'Thứ Hai',
            '2' => 'Thứ Ba',
            '3' => 'Thứ Tư',
            '4' => 'Thứ Năm',
            '5' => 'Thứ Sáu',
            '6' => 'Thứ Bảy'
        ];

        return $arr[$n];
    }
    
    public static function formatDate($string_date, $format='Y-m-d'){
        $string_date = str_replace(['.', '/'], '-', $string_date);
        return date($format, strtotime($string_date));
    }

    public static function formatDateTime($string_date, $format='Y-m-d H:i'){
        $string_date = str_replace(['.', '/'], '-', $string_date);
        return date($format, strtotime($string_date));
    }

    public static function randomStringNumberByLength($len = 2){

        $arr = [];
        for($i=0;$i<$len;$i++){
            $arr[] = rand(0, 9);
        }

        return implode('', $arr);

    }

    public static function getQuote($obj){
        return "'" . $obj . "'";
    }

    public static function formatCurrency($number, $decimal=0, $locale='vi_VN'){

        if($locale == 'vi_VN'){
            return number_format($number, $decimal, ',', '.');
        }

        return number_format($number);;

    }
    
    
}
