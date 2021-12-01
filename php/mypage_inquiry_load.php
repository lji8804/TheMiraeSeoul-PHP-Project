<?php
            // $option = $_POST['option'];
            if(isset($_GET['page'])){
                $current_page = $_GET['page'];
            }else{
                $current_page = 1;
            }
            
            if(isset($_GET['option'])){
                $option = $_GET['option'];
            }else{
                $option = 'title';
            }

            if(isset($_GET['search_str'])){
                $search_str = $_GET['search_str'];
            }else{
                $search_str = '';
                $flag = true;
            }
            

            $sql = "SELECT COUNT(*) AS `total_count` FROM inquiry WHERE id = '$id' ";
            if($option === 'written_date'){
                $sql .= "AND DATE($option) LIKE '%$search_str%' ";
            }else{
                $sql .= "AND $option LIKE '%$search_str%' ";
            }
            // 1. 총 게시글을 구한다
            // 쿼리문 실행
            $result = mysqli_query($con, $sql);
            // 배열에 결과값을 담는다
            $row = mysqli_fetch_assoc($result);
            // 총 게시글 갯수를 변수에 옮긴다.
            $total_count = $row['total_count'];
            
            // 게시판 한 페이지에 들어갈 게시글
            $page_row = 10;
            //총 페이지 갯수 구하기
            $total_page = ceil($total_count / $page_row);

            //URL에 넣은 page값을 받아온다.
            $first_index = ($current_page - 1) * $page_row;
            
            if($option === 'written_date'){
                $sql = "AND  DATE($option) LIKE '%$search_str%' ";
            }else{
                $sql = "AND  $option LIKE '%$search_str%' ";
            }
           
            $sql = "SELECT * FROM inquiry WHERE id = '$id'" .$sql. "ORDER BY written_date desc LIMIT {$first_index}, {$page_row} ";
            
            $result = mysqli_query($con, $sql);
            $list = array();
            for ($i = 0; $row = mysqli_fetch_assoc($result); $i++) {
                $list[$i] = $row;
                $list[$i]['num'] = $total_count - $first_index - $i;
            }
            
            //================================================ 여기까지가 테이블 세팅=========================================
            $http_host = $_SERVER['HTTP_HOST'];
            $request_uri = $_SERVER['REQUEST_URI'];
            $url = 'http://' . $http_host . $request_uri;

            $start_page = (int)(($current_page -1) / $page_row) * $page_row  + 1;
            $end_page = (($start_page + $page_row - 1) < $total_page) ? ($start_page + $page_row - 1) : $total_page;
            
            // HTML 메뉴 코드 추가하기
            $index_page = get_paging($page_row, $current_page, $total_page, $url);
            // 페이지 매기기
            