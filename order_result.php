﻿<!doctype html>
<html class="x-admin-sm">
<head>
    <meta http-equiv="Content-Type"  content="text/html;  charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="refresh" content="60" />
    <title>数据库上线工单查询</title>

<style type="text/css">
a:link { text-decoration: none;color: #3366FF}
a:active { text-decoration:blink;color: green}
a:hover { text-decoration:underline;color: #6600FF}
a:visited { text-decoration: none;color: green}
</style> 

    <script type="text/javascript" src="xadmin/js/jquery-3.3.1.min.js"></script>
    <script src="xadmin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="xadmin/js/xadmin.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./slowlog/css/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="./slowlog/css/font-awesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="./slowlog/css/styles.css">
</head>


<?php
session_start();
$prvi = $_SESSION['prvi'];
$login_user=$_SESSION['username'];

    if($prvi==0){
        echo "<br><h1>&nbsp;&nbsp;非法访问！你没有权限审批工单！</h1><br>";
        //echo '<meta http-equiv="Refresh" content="2;url=my_order.php"/>';
	exit;
    }
?>

<body>
<div class="card">
<div class="card-header bg-light">
    <h1>数据库上线工单查询</h1>
</div>

<div class="card-body">
<div class="table-responsive">

<?php	

$ops_order=$_GET['ops_order'];
$mysql_server_name='192.168.188.166';
$mysql_username='admin'; 
$mysql_password='wdhcy159753';
$mysql_database='sql_db';

$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting");
mysql_query("set names 'utf8'"); 
mysql_select_db($mysql_database);

$perNumber=50; //每页显示的记录数  
$page=$_GET['page']; //获得当前的页面值  
$count=mysql_query("select count(*) from sql_order_wait"); //获得记录总数  
$rs=mysql_fetch_array($count);   
$totalNumber=$rs[0];  
$totalPage=ceil($totalNumber/$perNumber); //计算出总页数  
/*if (!isset($page)) {  
 $page=1;  
} //如果没有值,则赋值1  */

if (empty($page)) {  
 $page=1;  
} //如果没有值,则赋值1

$startCount=($page-1)*$perNumber; //分页开始,根据此方法计算出开始的记录 

$sql1 = "select user from login_user where user = '${login_user}' and privilege in(1,2,3,100)";
$result1 = mysql_query($sql1,$conn);
if (mysql_num_rows($result1) > 0) {
	echo "Hi，管理员，等待你审批工单。<br>";
	$sql ="select a.*,b.real_user from sql_order_wait a join login_user b on a.ops_name = b.user where a.ops_order=${ops_order} order by id DESC limit $startCount,$perNumber";
}
$result = mysql_query($sql,$conn);

//echo "<h1 align='center' class='STYLE2'><a href='wait_order.php'>数据库上线工单查询</a></h1>";
//echo "<hr />";
echo "<br>";
/*echo "<form action='order_result.php' method='get'>
  <p align='left'>输入工单号:
    <input type='text' name='ops_order' value=''>
    <input name='submit'type='submit' value='查询' />
  </p>
</form>";*/
//echo "<table style='table-layout:fixed;word-break:break-all' class='bordered'  border='1' align='center'>";
//echo "<table style='table-layout:fixed;word-break:break-all' width='1000px' height='100px' border='1' align='center'>";
echo "<table style='table-layout:fixed;width:100%;font-size:15px;' class='table table-bordered'>";
echo "<tr>	
	    <th style='width:10%'>工单号</th>
            <th>申请人</th>
            <th>数据库名</th>
            <th>申请时间</th>
	    <th>工单名称</th>
	    <th>申请原因</th>
	    <th>审批结果</th>";
if($prvi!=1 && $prvi!=2 && $prvi!=3){
	   echo " <th>操作</th>
          </tr>";
}
while($row = mysql_fetch_array($result)) 
{
if(!preg_match("/alter/i",$row['ops_content'])){
$status = $row['status']?"<span style=''>已审批</span>":"<span class='badge badge-warning'><a href='update.php?id={$row['id']}&login_user=$login_user&prvi=$prvi'><font size='4'>待审批</font></a></span>";
//$status = $row['status']?"<span style=''>已审批</span>":"<span class='badge badge-warning'><a href='javascript:void(0);' onclick=\"x_admin_show('工单内容详细信息','update.php?id={$row['id']}&login_user=$login_user&prvi=$prvi')\"><font size='4'>待审批</font></a></span>";
$ddl_alter=0;
} else{
$status = $row['status']?"<span style=''>已审批</span>":"<span class='badge badge-warning'><a href='update_one.php?id={$row['id']}&login_user=$login_user&prvi=$prvi'><font size='4'>待业务线审批</font></a></span>";
$status_second = $row['status_second']?"<span style=''>已审批</span>":"<span class='badge badge-info'><a href='update_second.php?id={$row['id']}&login_user=$login_user&prvi=$prvi'><font size='4'>待大数据审批</font></a></span>";
$ddl_alter=1;
}
$exec_status = $row['status'];
$exec_status_second = $row['status_second'];
$exec_finish_status = $row['finish_status'];
$exec_finish_status_second = $row['finish_status_second'];
echo "<tr>";
echo "<td>{$row['ops_order']}</td>";
echo "<td>{$row['real_user']}</td>";
echo "<td>{$row['ops_db']}</td>";
echo "<td>{$row['ops_time']}</td>";
//echo "<td><a href='sql_statement.php?id={$row['id']}' target='_blank'>".$row['ops_order_name']."</a></td>";
echo "<td><a href='javascript:void(0);' onclick=\"x_admin_show('工单内容详细信息','sql_statement.php?id={$row['id']}')\">{$row['ops_order_name']}</a></td>";
echo "<td>{$row['ops_reason']}</td>";
//echo "<td style='word-wrap:break-word'>{$row['ops_content']}</td>";
if($prvi==1 || $prvi==2 || $prvi==3 || $prvi=100){
 if($ddl_alter==0){
	if($exec_status==0){
		echo "<td>$status</td>";
	}
	if($exec_status==1){
		echo "<td>$status</br>
		审批人:{$row['approver']}</td>";
	}
	if($exec_status==2){
		echo "<td>审批不通过</br>
		审批人：</br>{$row['approver']} </td>";
	}
  } else {
	
	if($exec_status==0 && $exec_status_second==0){
                echo "<td>$status</br>";
		echo "{$status_second}</td>";
        }
        if($exec_status==1 && $exec_status_second==0){
               echo "<td>$status</br>
                业务线审批人:{$row['approver']}</br>";
		echo "</br>";
		echo "<span class='badge badge-info'><a href='update_second.php?id={$row['id']}&login_user=$login_user&prvi=$prvi'><font size='4'>待大数据审批</font></a></span></td>";
        }
	if($exec_status_second==1){
		if($exec_status==1){
                	echo "<td>全部审批通过</br>
                	业务线审批人:{$row['approver']}</br>";
                	echo "</br>";
                	echo "大数据审批人:{$row['approver2']}</td>";
		}
		else if($exec_status==0){
   			echo "<td>大数据已审批通过</br>
			     审批人：{$row['approver2']}</br>";
			echo "<span class='badge badge-warning'><a href='update_one.php?id={$row['id']}&login_user=$login_user&prvi=$prvi'><font size='4'>待业务线审批</font></a></span></td>";
		}
		else {
			//echo "<td >审批不通过</br>
		//	业务线审批人:{$row['approver']}</td>";
		}
        }
	
	if($exec_status_second==2){
		echo "<td>审批不通过</br>
		大数据审批人：</br>{$row['approver2']} </td>";
        }
	if($exec_status==2){
		echo "<td>审批不通过</br>
		业务线审批人:{$row['approver']}</td>";
	}
  }
}
else{
	echo "<td>等待审批中</td>";
}
#######################################################
if($prvi==100){

if($ddl_alter==0){
if($exec_finish_status==1){
	echo "<td ><a href='execute.php?id={$row['id']}'>执行工单</a></td>";
	//echo "<a href='cancel.php?id={$row['id']}'>自行撤销工单</a></td>";
}
else if($exec_finish_status==2){
	echo "<td >已执行</br>";
	echo "<a href='rollback.php?id={$row['id']}'>生成反向SQL</a></td>";
}
else{
	echo "<td >没审批不能执行</br>
	<a href='cancel.php?id={$row['id']}'>自行撤销工单</a>
	</td>";
	//echo "<td width='80'><a href='cancel.php?id={$row['id']}'>自行撤销工单</a></td>";
} 
echo "</tr>";
} else{
	if($exec_finish_status==1 && $exec_finish_status_second==1){
		echo "<td ><a href='execute.php?id={$row['id']}'>执行工单</a></td>";
		//echo "<a href='cancel.php?id={$row['id']}'>自行撤销工单</a></td>";
	} else if ($exec_finish_status_second==2){
		echo "<td >已执行</br>";
		echo "<a href='rollback.php?id={$row['id']}'>生成反向SQL</a></td>";
	} else {
		echo "<td >没审批不能执行</br>
		<a href='cancel.php?id={$row['id']}'>自行撤销工单</a>
		</td>";
	}        
}
} //end if($prvi==1)

}//end while
echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

$maxPageCount=10; 
$buffCount=2;
$startPage=1;
 
if  ($page< $buffCount){
    $startPage=1;
}else if($page>=$buffCount  and $page<$totalPage-$maxPageCount  ){
    $startPage=$page-$buffCount+1;
}else{
    $startPage=$totalPage-$maxPageCount+1;
}
 
$endPage=$startPage+$maxPageCount-1;
 
 
$htmlstr="";
 
$htmlstr.="<table class='bordered' border='1' align='center'><tr>";
    if ($page > 1){
        $htmlstr.="<td> <a href='wait_order.php?page=" . "1" . "'>第一页</a></td>";
        $htmlstr.="<td> <a href='wait_order.php?page=" . ($page-1) . "'>上一页</a></td>";
    }

    $htmlstr.="<td> 总共${totalPage}页</td>";

    for ($i=$startPage;$i<=$endPage; $i++){
         
        $htmlstr.="<td><a href='wait_order.php?page=" . $i . "'>" . $i . "</a></td>";
    }
     
    if ($page<$totalPage){
        $htmlstr.="<td><a href='wait_order.php?page=" . ($page+1) . "'>下一页</a></td>";
        $htmlstr.="<td><a href='wait_order.php?page=" . $totalPage . "'>最后页</a></td>";
 
    }
$htmlstr.="</tr></table>";
echo $htmlstr;

?>


</html>
