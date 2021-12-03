<!-- 
<데이터베이스와의 통신 고정 순서>
1. 쿼리문 준비($sql)
2. 쿼리문 전송(mysqli_query($con, $sql)) 후 결과($result) 받기
3. 결과로 값 불러오기(mysqli_fetch_row(인덱스)/assoc(연관배열)/array(인덱스 및 연관배열)) 
-->

<?php
    // 1. 데이터베이스 시간 설정
    date_default_timezone_set("Asia/Seoul");

    // 2. 데이터베이스 접속 및 오류처리
    // 2-1. 접속
    $mysql_host = "localhost";
    $mysql_id = "root";
    $mysql_password = "123456";
    $con = mysqli_connect($mysql_host, $mysql_id, $mysql_password);
    // 2-2. 오류처리
    if(!$con) {
        die("데이터베이스 연결 실패".mysqli_connect_errno());
    }

    // 3. 데이터베이스가 이미 존재하는 지 여부 확인
    $database_flag = false;
    // 3-1. 쿼리문 생성
    $sql = "SHOW DATABASES";
    // 3-2. 쿼리문 전송 및 결과 받기
    $result = mysqli_query($con, $sql) or die("데이터베이스 확인 실패".mysqli_error($con));
    // 3-3. 결과로부터 값 불러오기
    while($row = mysqli_fetch_array($result)) {
        // 'Database'열을 확인하여 'theMiraeSeoul'이 있을 경우, flag = true로 변경
        if($row["Database"] == "themiraeseoul") {
            $database_flag = true;
            break;
        }
    }

    // 4. 데이터베이스가 존재하지 않을 경우 생성
    if($database_flag === false) {
        // 4-1. 쿼리문 생성
        $sql = "CREATE DATABASE theMiraeSeoul";
        // 4-2. 쿼리문 전송 및 결과 받기
        $value = mysqli_query($con, $sql) or die("데이터베이스 생성 실패".mysqli_error($con));
        // 4-3. 결과 확인
        if($value === true) {
            echo "<script>alert('theMiraeSeoul 데이터베이스가 생성되었습니다.')</script>";
        }
    }

    // 5. (쿼리문을 보낼 기본)데이터베이스 선택 및 오류처리
    // 5-1. 선택
    $dbcon = mysqli_select_db($con, "theMiraeSeoul") or die("데이터베이스 선택 실패".mysqli_error($con));
    // 5-2. 오류처리
    if(!$dbcon) {
        echo "<script>alert('theMiraeSeoul 데이터베이스 선택에 실패했습니다.')</script>";
    }

    // 6. 공용 함수 생성
    // 6-1. 메세지 표시 후 뒤로가기 함수
    function alert_back($message) {
        echo "<script>alert($message);</script>";
        echo "<script>history.go(-1);</script>";
        exit;
    }

    // 6-2. 데이터 결함 방어 함수
    function input_check($data) {
        $data = trim($data); // 공백 방어
        $data = stripslashes($data); // 슬래시 방어
        $data = htmlspecialchars($data); // 특수문자 방어
        return $data;
    }

    // 6-3. MySQL 인젝션 방어 함수
    function sql_escape($conn, $content) {
        return mysqli_real_escape_string($conn, $content);
    }

    function get_paging($write_pages, $cur_page, $total_page, $url)
  {
      //memo_login&page123 => memo_login&page= 변환시켜달라 
      $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';
  
      $str = '';
      //1. 현재페이지가 1페이지가 아니고 2페이지 이상이라면 처음가기를 등록한다.  
      if ($cur_page > 1) {
          $str .= '<a href="'.$url.'1" class="pg_page pg_start">처음</a>'.PHP_EOL;
      }
  
      //2 시작페이지와 끝페이지를 등록한다.(현재12page 시작페이지: 11 ~ 끝페이지 20) 
    // 끝페이지가 중요함.(총 56페이지일때 현재52페이지 시작페이지: 51 ~ 끝페이지 60)
    // 끝페이지 >= 총페이지보다 크거나 같으면 끝페이지 = 총페이지 시작: 51 ~ 끝페이지 56)
      $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
      $end_page = $start_page + $write_pages - 1;
      if ($end_page >= $total_page) $end_page = $total_page;
  
      //3 시작페이지가 2페이지 이상이면 [이전]  시작페이지 -1
    //[처음][이전][11]스트롱[12]스트롱[13]...[19][20] => [처음][이전][1][2][3]...[9]스트롱[10]스트롱
      if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).'" class="pg_page pg_prev">이전</a>'.PHP_EOL;
  
      //4 전체페이지가 2이상 이고 시작페이지 11페이지 끝페이지 20페이지면 현재페이지 12페이지
    //[처음][이전][11]스트롱[12]스트롱[13]...[19][20]
      if ($total_page > 1) {
          for ($k=$start_page;$k<=$end_page;$k++) {
              if ($cur_page != $k)
                  $str .= '<a href="'.$url.$k.'" class="pg_page">'.$k.'</a>'.PHP_EOL;
              else
                  $str .= '<strong class="pg_current">'.$k.'</strong>'.PHP_EOL;
          }
      }
  
      //5 전체페이지 56 > 20페이지라면 [다음]
    //[처음][이전][11]스트롱[12]스트롱[13]...[19][20][다음] => [처음][이전]스트롱[21]스트롱[22][23]...[29][30]
      if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).'" class="pg_page pg_next">다음</a>'.PHP_EOL;
  
      //6 현재페이지가 전체페이지보다 작다면 [처음][이전][11]스트롱[12]스트롱[13]...[19][20][다음][끝]
      if ($cur_page < $total_page) {
          $str .= '<a href="'.$url.$total_page.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
      }
  
      //7 $str 페이징 문자열이 만들어 졌다면 생성
      if ($str)
          return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
      else
          return "";
      }
?>