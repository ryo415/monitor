<?php
const HTTP_STATUS_OK_VALUE = '1';
const HTTP_STATUS_NG_VALUE = '0';

function get_labels($time, $timeList, $head)
{
    #同じ時間帯なら目盛を省略
    if ($time == $head) {
        array_push($timeList, "");
    } else {
        array_push($timeList, $time);
        $head = $time;
    }

    return array($timeList, $head);
}

function get_times($statusList, $status, $status_flag_List, $response_time_List, $response_time)
{
    array_push($statusList, $status);
    #ステータスが200以外ならレスポンスタイムを0にする
    if ($status == 200) {
        array_push($status_flag_List, HTTP_STATUS_OK_VALUE);
        array_push($response_time_List, $response_time);
    } else {
        array_push($status_flag_List, HTTP_STATUS_NG_VALUE);
        array_push($response_time_List, "0");
    }

    return array($statusList, $status_flag_List, $response_time_List);
}

#最初にページが開かれた際の出力
if (array_key_exists('date', $_POST)) {
    $post_date = htmlspecialchars($_POST['date']);
} else {
    echo "<font>日時を選択してください</font>";
    exit(0);
}
$filename = "../data/http_response-".$post_date.".txt";

#日時が選択されていない場合のエラー
if (empty($post_date)) {
    echo "<font color='red'>error: 日時を選択してください</font>";
    exit(0);
}

#選択された日時のファイルが存在しない場合のエラー
if (file_exists($filename)) {
    $data = file($filename);
} else {
    echo "<font color='red'>error: 該当日時データなし</font>";
    exit(0);
}

$post_time = htmlspecialchars($_POST['time']);
$hourList = array();
$minuteList = array();
$statusList = array();
$status_flag_List = array();
$response_time_List = array();
$head = "";

#該当ファイルは存在するが中身のデータがない場合のエラー
if ($data==false) {
    echo "<font color='red'>error: 該当日時データなし</font>";
    exit(0);
}

for ($i=0; $i<count($data); $i++) {
    $split_data = explode(' ', $data[$i]);
    $split_time = explode(':', $split_data[1]);

    $hour = $split_time[0];
    $minute = $split_time[1];
    $status = $split_data[2];
    $response_time = trim($split_data[3]);

    if ($post_time=="all") {
        list($hourList, $head) = get_labels($hour, $hourList, $head);
        list($statusList, $status_flag_List, $response_time_List) = get_times($statusList, $status, $status_flag_List, $response_time_List, $response_time);
    } else {
        #指定した時間のみのデータを保持
        if ($post_time == $hour) {
            list($minuteList, $head) = get_labels($minute, $minuteList, $head);
            list($statusList, $status_flag_List, $response_time_List) = get_times($statusList, $status, $status_flag_List, $response_time_List, $response_time);
        }
    }
}


#それぞれの配列をコンマ区切りの文字列として保持
if ($post_time=="all") {
    $implode_time = implode(", ", $hourList);
    $unit = "時";
    $title = $post_date . ' 全日'. ':HTTPレスポンスステータス';
} else {
    #指定された時間のデータがない場合のエラー
    if (empty($minuteList)) {
        echo "<font color='red'>error: 該当時刻データなし</font>";
        exit(0);
    }
    $implode_time = implode(", ", $minuteList);
    $unit = "分";
    $title = $post_date. ' '. $post_time .'時台 :HTTPレスポンスステータス';
}
$implode_status_flag = implode(", ", $status_flag_List);
$implode_response_time = implode(", ", $response_time_List);

$js_chart = <<<EOM
    <div>
      <canvas id="ResponseStatusChart" width="1000"></canvas>
    </div>
    <div>
      <canvas id="ResponseTimeChart" width="1000"></canvas>
    </div>
    <script src="js/Chart.bundle.js"></script>
EOM;

$js_response_status = <<<EOM
<script>
    var ctx = document.getElementById("ResponseStatusChart");
    var ResponseStatusChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [ $implode_time ],
        datasets: [
          {
            label: 'ステータス',
            data: [ $implode_status_flag ],
            borderColor: "rgba(255,0,0,1)",
            backgroundColor: "rgba(0,0,0,0)",
            pointRadius: 0
          },
        ],
      },
      options: {
        responsive: false,
        title: {
          display: true,
          text: '$title'
        },
        scales: {
          yAxes: [{
            ticks: {
             callback: function(value, index, values){
             return  value
              }
            }
          }],
         xAxes: [{
           ticks: {
              suggestedMax: 24,
              suggestedMin: 0,
              stepSize: 1,
              callback: function(value, index, values){
                return value + '$unit'
              }
            }
          }],
        },
      }
    });
    </script>

EOM;

$js_response_time = <<<EOM
<script>
    var ctx = document.getElementById("ResponseTimeChart");
    var ResponseTimeChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [ $implode_time ],
        datasets: [
          {
            label: 'レスポンスタイム',
            data: [ $implode_response_time ],
            borderColor: "rgba(255,0,0,1)",
            backgroundColor: "rgba(0,0,0,0)"
          },
        ],
      },
      options: {
        responsive: false,
        title: {
          display: true,
          text: '$title'
        },
        scales: {
          yAxes: [{
            ticks: {
             callback: function(value, index, values){
             return  value +  'sec'
              }
            }
          }],
         xAxes: [{
           ticks: {
              suggestedMax: 24,
              suggestedMin: 0,
              stepSize: 1,
              callback: function(value, index, values){
                return value + '$unit'
              } 
            }
          }],
        },
      }
    });
    </script>
EOM;

echo "$js_chart";
echo "$js_response_status";
echo "$js_response_time";
