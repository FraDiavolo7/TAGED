// script pour les besoin JS de Match 3 liés à TAGED.


function tagedSendRequest ( Request, DataSet ) 
{
    console.log ( "tagedSendRequest : " + Request );

    url = 'https://178.170.138.12:14930/ws.php?sel=m3&m3=' + Request;

    var data = new FormData();
    data.append('data', DataSet);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.send(data);

/*
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({
    value: 'value'
}));
/*
xhr.send(JSON.stringify({
    value: DataSet
    }));
*/
}
