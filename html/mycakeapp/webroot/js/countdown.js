//フォームにて設定した終了時刻
let end = document.querySelector('#end');
let request = new XMLHttpRequest;
request.open('HEAD', '#', false);
request.send(null);
//サーバーから取得している現在時刻
let start = new Date(request.getResponseHeader('Date'));


function countdown() {
    let rest = new Date(end.innerHTML) - start.setTime(start.getTime()+1000);
    //オークションが終了していない場合
    if (rest >= 0) {
        let hour = Math.floor(rest / (1000 * 60 * 60));
        rest -= (hour * 1000 * 60 * 60);
        let minutes = Math.floor(rest / (1000 * 60));
        rest -= (minutes * 1000 * 60);
        let second = Math.floor(rest / 1000);
        let insert = "";
        insert += '<span class="h">' + hour + ':' + '<span>'; 
        insert += '<span class="m">' + minutes + ':' + '<span>';
        insert += '<span class="s">' + second + '<span>';
        document.querySelector('#endtime').innerHTML = insert;
        setTimeout(countdown, 1000);
    //オークションが終了している場合
    } else {
        document.querySelector('#endtime').innerHTML = '<h1>オークションは、終了しました。<h1>';
    }
}
countdown();