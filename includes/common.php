<?
  define("TABLE_SETTINGS",	"amocrm_settings");
  define('TABLE_USERS',		'amocrm_users');
  define('TABLE_TASKS',		'amocrm_tasks');
  define('TABLE_CONTACTS',	'amocrm_contacts');
  define('TABLE_LEADS',		'amocrm_leads');
  define('TABLE_NOTES',		'amocrm_notes');
  
  



////////////////////////////////////
  function get_extension( $filename )
  {
    return substr ( strrchr($filename,"."), 1, strlen ( $filename ) );
  }
////////////////////////////
  function make_sql_query($fields_arr, $table, $action, $where_cond = '', $fields_update_arr = array()){
    $the_action = strtoupper($action);
    if(!in_array($the_action, array('INSERT', 'UPDATE', 'DELETE')))
      return;
    if(!count($fields_arr) || !$table)
      return;

    $the_where_cond = "WHERE 1";
    if($where_cond)
      $the_where_cond .= " AND ($where_cond)";

    $field_names_arr = array();
    $field_values_arr = array();

    if($the_action == 'INSERT') {
      foreach ($fields_arr as $fields) {
        $field_names_arr[] = "`".$fields['name']."`";
        $field_values_arr[] = "'".@mysql_escape_string($fields['value'])."'";
      }

      $field_names = implode(",", $field_names_arr);
      $field_values = implode(",", $field_values_arr);
      if(count($fields_update_arr)){
        $set_arr = array();
        foreach ($fields_update_arr as $field) {
          $set_arr[] = "`$field` = VALUES(`$field`)";
        }
        $set_str = implode(', ', $set_arr);
		    		       	
        $onduplicatekey = "ON DUPLICATE KEY UPDATE " . $set_str;
      }
      else
        $onduplicatekey = '';

      $sql = "INSERT IGNORE INTO $table($field_names) VALUES($field_values) $onduplicatekey";
    }
    elseif($the_action == 'UPDATE') {
      $set_arr = array();
      foreach ($fields_arr as $fields) {
        $set_arr[] = "`".$fields['name']."`" . " = " . "'" . @mysql_escape_string($fields['value']) . "'";
      }
      $set_str = implode(', ', $set_arr);

      $field_names = implode(",", $field_names_arr);
      $field_values = implode(",", $field_values_arr);

      $sql = "UPDATE $table SET $set_str $the_where_cond";
    }



    return $sql;
  }
////////////////

  function mywget($url){

///$user_cookie_file = $_SERVER['DOCUMENT_ROOT'].'/data/mycookie.txt'; 
//die($user_cookie_file);
///@unlink($user_cookie_file);


    $ch = curl_init();                              // �������������� ����� CURL

///    curl_setopt($ch, CURLOPT_HEADER, true);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    
    

    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)"); 


    curl_setopt($ch, CURLOPT_URL, $url);            // ������� �� ����
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // ������ ���, ����� �������� �� ���������� ����� � �����, � ����� ���� �� �������� � ����������
///    curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file);  // ���������� cookies � ����, ����� ����� ����� ���� �� �������
///    curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); // ������ ������ cookies � �����
///    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // ������ ������, ��� ����� �������� ������ ���������� �������� heaer('Location:...').

    $html = curl_exec($ch);                         // ��������� ����� �� ����
/*
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest")); 
    curl_setopt($ch, CURLOPT_URL, $urlTo);              // ������������� ����� ���� ����� ����� POST ������
    curl_setopt($ch, CURLOPT_POST, true);               // �������, ��� ���������� ����� ������������ ������� POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);        // �������� POST ������
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // ������ ������, ��� ����� �������� ������ ���������� �������� heaer('Location:...').

    $html = curl_exec($ch); // ���������� ��������� ������ � ����������


    curl_setopt($ch, CURLOPT_URL, $url_private);              // ������������� �����
    $html = curl_exec($ch); // ���������� ��������� ������ � ����������
*/

    curl_close($ch);        // ��������� ����� ������ CURL
    return $html;
  }



  function get_month_number($month_str){
	$ret_value = 0;

	$the_month_str = strtolower(trim($month_str));

	$months = array();
        $months[1] = "�����";
        $months[2] = "������";
        $months[3] = "����";
        $months[4] = "�����";
        $months[5] = "��";
        $months[6] = "���";
        $months[7] = "���";
        $months[8] = "������";
        $months[9] = "�������";
        $months[10] = "������";
        $months[11] = "�����";
        $months[12] = "������";

        $count = count($months);

        for ($i = 1; $i <= $count; $i++) {
          if ($i == 3 || $i == 8)
            $months[$i] .= '�';
          else
            $months[$i] .= '�';
        }

        $ret_value = array_search($the_month_str, $months);

	return $ret_value;
  }


?>