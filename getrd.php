<?php
//  session_start();
  define('MY_AUTH_KEY', 'JsDf6537');

  include "includes/common.php";
  include "includes/db.php";

  $total_records_received = 0;

  $the_auth_key = isset($_GET['smk']) ? $_GET['smk'] : '';
  if($the_auth_key != MY_AUTH_KEY)
    die('Error: No credential');

  $db = new db();

  list($the_login, $the_api_key, $the_account) = getSettings();

  if(!$the_login || !$the_api_key || !$the_account)
    die('Error: No credentials');

  if(!sm_auth($the_login, $the_api_key, $the_account))
    die('Error: Authorization failed');

  $total_records_received = getRemoteData($the_account);

  $update_details = showUpdateDetails($total_records_received);

  die("Result for $the_account:<br>" . $update_details);

//////////////// functions below //////////////////
  function markDeletedRecords($table, $present_ids_arr){
    global $db;
    if(count($present_ids_arr)){
      $present_ids_arr_str = implode(',', $present_ids_arr);
      $where_cond = "WHERE is_deleted=0 AND id NOT IN($present_ids_arr_str)";
      $sql = "UPDATE $table SET is_deleted=1 $where_cond";
      $db->exec_query($sql);//tmp!!!

      $where_cond = "WHERE is_deleted=1 AND id IN($present_ids_arr_str)";
      $sql = "UPDATE $table SET is_deleted=0 $where_cond";
      $db->exec_query($sql);//tmp!!!
    }
  } 
////////////////////////////////////////////////////////////////////
  function showUpdateDetails($values_arr){
    $ret_value = '';
    foreach ($values_arr as $key => $value) {
      $tmp_row = "$key: {$value['received']} records received, {$value['affected']} records affected";	
      $ret_value .= $tmp_row . "<br>";
    }
     
    return $ret_value;
  }//function showUpdateDetails($values_arr)
////////////////////////////////////////////////////////////////////
  function getSettings(){
    global $db;

    $login		= '';
    $api_key	= '';
    $account	= '';



    $table = TABLE_SETTINGS;
    $sql = "SELECT * FROM $table";
    $db->exec_query($sql);
    while($db->get_data()){
      extract($db->result);

      switch ($setting_name) {
      	case 'login':
      	  $login = $setting_value;
     	   break;
      	case 'api_key':
      	  $api_key = $setting_value;
     	   break;
      	case 'account':
      	  $account = $setting_value;
     	   break;
      }
    }

    return array($login, $api_key, $account);
  }//function getSettings()
////////////////////////////////////////////////////////////////////
  function getRemoteData($subdomain){
    $ret_value = array();

    $ret_value['users']['received'] = 0;
    $ret_value['users']['affected'] = 0;

    $ret_value['tasks']['received'] = 0;
    $ret_value['tasks']['affected'] = 0;

    $ret_value['contacts']['received'] = 0;
    $ret_value['contacts']['affected'] = 0;

    $ret_value['leads']['received'] = 0;
    $ret_value['leads']['affected'] = 0;

    $ret_value['notes']['received'] = 0;
    $ret_value['notes']['affected'] = 0;

    $max_cycles = 10;                                         
    $sm_limit = 500;//amocrm system limit

    $i = 0;
    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

		$Response = getJsonData('accounts/current' . $limit_link, $subdomain);
//print_r($Response);
//die();
		$values_arr = isset($Response['account']['users']) ? $Response['account']['users'] : array();

		if(!count($values_arr))
		  break;

		$ret_value['users']['received'] += count($values_arr);
		$ret_value['users']['affected'] += getUsers($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
    markDeletedRecords(TABLE_USERS, $present_items_arr);

    $i = 0;
    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

	    $Response = getJsonData('tasks/list' . $limit_link, $subdomain);
//print_r($Response);
//die();
	    $values_arr = isset($Response['tasks']) ? $Response['tasks'] : array();

		if(!count($values_arr))
		  break;

	    $ret_value['tasks']['received'] += count($values_arr);
	    $ret_value['tasks']['affected'] += getTasks($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
    markDeletedRecords(TABLE_TASKS, $present_items_arr);

    $i = 0;
    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

	    $Response = getJsonData('contacts/list' . $limit_link, $subdomain);
//print_r($Response);
//die();
	    $values_arr = isset($Response['contacts']) ? $Response['contacts'] : array();

		if(!count($values_arr))
		  break;

	    $ret_value['contacts']['received'] += count($values_arr);
	    $ret_value['contacts']['affected'] += getContacts($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
    markDeletedRecords(TABLE_CONTACTS, $present_items_arr);


    $i = 0;
    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

	    $Response = getJsonData('leads/list' . $limit_link, $subdomain);
//print_r($Response);
//die();
	    $values_arr = isset($Response['leads']) ? $Response['leads'] : array();

		if(!count($values_arr))
		  break;

	    $ret_value['leads']['received'] += count($values_arr);
	    $ret_value['leads']['affected'] += getLeads($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
//    markDeletedRecords(TABLE_LEADS, $present_items_arr);


    $i = 0;
    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

        $type_link = "&type=contact";
	    $Response = getJsonData('notes/list' . $limit_link . $type_link, $subdomain);
//print_r($Response);
//die();
	    $values_arr = isset($Response['notes']) ? $Response['notes'] : array();

		if(!count($values_arr))
		  break;

	    $ret_value['notes']['received'] += count($values_arr);
	    $ret_value['notes']['affected'] += getNotes($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
//    markDeletedRecords(TABLE_NOTES, $present_items_arr);

    $i = 0;
//    $present_items_arr = array();
    while($i < $max_cycles){
		$limit_link = "?limit_rows=$sm_limit&limit_offset=" . ($i * $sm_limit);
        $i++;

        $type_link = "&type=lead";
	    $Response = getJsonData('notes/list' . $limit_link . $type_link, $subdomain);
//print_r($Response);
//die();
	    $values_arr = isset($Response['notes']) ? $Response['notes'] : array();

		if(!count($values_arr))
		  break;

	    $ret_value['notes']['received'] += count($values_arr);
	    $ret_value['notes']['affected'] += getNotes($values_arr, $present_items_arr);

		if(count($values_arr) < $sm_limit)
		  break;
    }
    markDeletedRecords(TABLE_NOTES, $present_items_arr);


    


    return $ret_value;
  }//function getRemoteData()
////////////////////////////////////////////////////////////////////
function getTasks($values_arr, &$present_items_arr){
  $table = TABLE_TASKS;

  $the_fields = array(
  'id'
  , 
  'element_id'
  ,
  'element_type'
  ,
  'task_type'
  ,
  'date_create'
  ,
  'created_user_id'
  ,
  'last_modified'
  ,
  'text'
  ,
  'responsible_user_id'
  ,
  'complete_till'
  ,
  'status'
  ,
  'group_id'
  ,
  'account_id'
  );

  $the_fields_duplicate = array(
  'element_id'
  ,
  'element_type'
  ,
  'task_type'
  ,
  'responsible_user_id'
  ,
  'status'
  );

  $time_arr = array(
  'date_create'
  ,
  'last_modified'
  ,
  'complete_till'
  );

  $ret_value = getData($values_arr, $table, $the_fields, $the_fields_duplicate, $time_arr, $present_items_arr);

  return $ret_value;
}//function getTasks($values_arr)

////////////////////////////////////////////////////////////////////
function getUsers($values_arr, &$present_items_arr){
  $table = TABLE_USERS;

  $the_fields = array(
  'id'
  , 
  'name'
  ,
  'last_name'
  ,
  'login'
  ,
  'group_id'
  );

  $the_fields_duplicate = array_slice($the_fields, 1);

  $ret_value = getData($values_arr, $table, $the_fields, $the_fields_duplicate, array(), $present_items_arr);

  return $ret_value;
}//function getUsers($values_arr)
////////////////////////////////////////////////////////////////////
function getContacts($values_arr, &$present_items_arr){
  $table = TABLE_CONTACTS;

  $the_fields = array(
  'id'
  , 
  'name'
  ,
  'company_name'
  ,
  'type'
  ,
  'created_user_id'
  ,
  'last_modified'
  ,
  'date_create'
  ,
  'responsible_user_id'
  );

  $the_fields_duplicate = array_slice($the_fields, 1);

  $time_arr = array(
  'date_create'
  ,
  'last_modified'
  );

  $ret_value = getData($values_arr, $table, $the_fields, $the_fields_duplicate, $time_arr, $present_items_arr);

  return $ret_value;
}//function getContacts($values_arr)
////////////////////////////////////////////////////////////////////
function getLeads($values_arr, &$present_items_arr){
  $table = TABLE_LEADS;

  $the_fields = array(
  'id'
  , 
  'name'
  ,
  'date_create'
  ,
  'created_user_id'
  ,
  'last_modified'
  ,
  'status_id'
  ,
  'price'
  ,
  'responsible_user_id'
  ,
  'deleted'
  );

  $the_fields_duplicate = array_slice($the_fields, 1);

  $time_arr = array(
  'date_create'
  ,
  'last_modified'
  );

  $ret_value = getData($values_arr, $table, $the_fields, $the_fields_duplicate, $time_arr, $present_items_arr);

  return $ret_value;
}//function getLeads($values_arr)

////////////////////////////////////////////////////////////////////
function getNotes($values_arr, &$present_items_arr){
  $table = TABLE_NOTES;

  $the_fields = array(
  'id'
  , 
  'element_id'
  ,
  'element_type'
  ,
  'note_type'
  ,
  'date_create'
  ,
  'created_user_id'
  ,
  'last_modified'
  ,
  'text'
  ,
  'responsible_user_id'
  ,
  'editable'
  );

  $the_fields_duplicate = array(
  'element_id'
  ,
  'element_type'
  ,
  'note_type'
  ,
  'last_modified'
  ,
  'responsible_user_id'
  ,
  'editable'
  );

  $time_arr = array(
  'date_create'
  ,
  'last_modified'
  );

  $ret_value = getData($values_arr, $table, $the_fields, $the_fields_duplicate, $time_arr, $present_items_arr);

  return $ret_value;
}//function getNotes($values_arr)

////////////////////////////////////////////////////////////////////
function getData($values_arr, $table, $the_fields, $the_fields_duplicate, $time_arr = array(), &$present_items_arr){
  global $db;

  $diff_hours = - (1 * 60 * 60);//for Moscow time zone

  $present_ids_arr = array();
  $ret_value = 0;
  foreach ($values_arr as $value) {
    $present_items_arr[] = $value['id'];
    $fields_arr = array();

    foreach ($the_fields as $the_field) {
        $the_value = $value[$the_field];
        if(in_array($the_field, $time_arr)){
          if(isset($diff_hours))
            $the_value += $diff_hours;

          $the_value = date('Y-m-d H:i:s', $the_value); //convert from unix timestamp to mysql datetime (timestamp)
        }

        $tmp_arr = array(
    	  'name'  => $the_field,   
    	  'value' => $the_value  
        );
        $fields_arr[] = $tmp_arr;
    }

    $sql = make_sql_query($fields_arr, $table, 'INSERT', '', $the_fields_duplicate);
    $db->exec_query($sql);

    $ret_value += $db->affected_rows;
  }

  return $ret_value;
}//function getData($values_arr, $table, $the_fields, $the_fields_duplicate)

////////////////////////////////////////////////////////////////////
function CheckCurlResponse($code){
	$code=(int)$code;
	$errors=array(
		301=>'Moved permanently',
		400=>'Bad request',
		401=>'Unauthorized',
		403=>'Forbidden',
		404=>'Not found',
		500=>'Internal server error',
		502=>'Bad gateway',
		503=>'Service unavailable'
	);
	try
	{
		#Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
		if($code!=200 && $code!=204)
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
	}
	catch(Exception $E)
	{
		die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
	}
}//function CheckCurlResponse($code)

////////////////////////////////////////////////////////////////////
function sm_auth($the_login, $the_api_key, $the_account){
  #Массив с параметрами, которые нужно передать методом POST к API системы
  $user=array(
  	'USER_LOGIN' => $the_login, #Ваш логин (электронная почта)
  	'USER_HASH'  => $the_api_key #Хэш для доступа к API (смотрите в профиле пользователя)
  );
   
  $subdomain = $the_account; #Наш аккаунт - поддомен
  #Формируем ссылку для запроса
  $link = 'https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
  $curl = curl_init(); #Сохраняем дескриптор сеанса cURL
  #Устанавливаем необходимые опции для сеанса cURL
  curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
  curl_setopt($curl,CURLOPT_URL,$link);
  curl_setopt($curl,CURLOPT_POST,true);
  curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($user));
  curl_setopt($curl,CURLOPT_HEADER,false);
  curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
  curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

  $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
  $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
  curl_close($curl); #Заверашем сеанс cURL
  CheckCurlResponse($code);
  /**
   * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
   * нам придётся перевести ответ в формат, понятный PHP
   */
  $Response=json_decode($out,true);
  $Response=$Response['response'];

  if(isset($Response['auth'])) #Флаг авторизации доступен в свойстве "auth"
    $ret_value = 1;
  //	echo 'Authorization successfull';
  else {
    $ret_value = 0;
  }

  return $ret_value;
}//function sm_auth($the_login, $the_api_key, $the_account)
////////////////////////////////////////////////////////////////////
function getJsonData($data_type, $subdomain){

    //$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/tasks/list'; 
    $link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/'.$data_type; 

//echo $link."<br>";

    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);
    CheckCurlResponse($code);
    /**
     * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     * нам придётся перевести ответ в формат, понятный PHP
     */
    $Response = json_decode($out,true);
    return $Response['response'];
}
////////////////////////////////////////////////////////////////////
?>