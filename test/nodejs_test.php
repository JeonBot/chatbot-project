<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
<script>

var socket = io.connect('http://52.78.109.0:3305');

socket.emit('call', '5');//검색어를 보낸다

socket.on('answer', function(data){
        $('#answer').text(data);
});
</script>

<div id="answer"></div>